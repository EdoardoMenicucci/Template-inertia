Per creare un nuovo progetto Laravel specificando la versione 10, puoi aggiungere il numero di versione al comando `create-project` di Composer. Ecco come fare:

```bash
composer create-project --prefer-dist laravel/laravel nome_progetto "10.*"
```

Questo comando creerà un nuovo progetto Laravel utilizzando la versione 10.x.

### Passaggi Successivi

Dopo aver creato il progetto, puoi procedere con l'installazione di Laravel Breeze, Inertia.js e Vue.js come descritto in precedenza. Ecco un riepilogo dei passaggi successivi:

### 1. Installazione di Laravel Breeze

Installa Laravel Breeze:

```bash
composer require laravel/breeze --dev
```

### 2. Installazione di Breeze con Inertia.js e Vue.js

Esegui il comando per installare Breeze con Inertia.js:

```bash
php artisan breeze:install vue
```

### 3. Installazione delle Dipendenze JavaScript

Installa le dipendenze JavaScript:

```bash
npm install
npm run dev
```

### 4. Esecuzione delle Migrazioni

Esegui le migrazioni per creare le tabelle necessarie per l'autenticazione:

```bash
php artisan migrate
```

### 5. Configurazione di Ruoli e Permessi

Installa il pacchetto Spatie Laravel Permission per la gestione dei ruoli e dei permessi:

```bash
composer require spatie/laravel-permission
```

Pubblica il file di configurazione e le migrazioni:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 6. Configurazione del Modello Utente

Aggiungi il trait `HasRoles` al modello `User`:

```php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // Altre configurazioni del modello
}
```

### 7. Creazione dei Ruoli e delle Autorizzazioni

Puoi creare ruoli e autorizzazioni nel `DatabaseSeeder` o in un seeder dedicato:

```php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crea ruoli
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'moderator']);
        Role::create(['name' => 'user']);

        // Crea autorizzazioni
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'edit articles']);

        // Assegna autorizzazioni ai ruoli
        $admin = Role::findByName('admin');
        $admin->givePermissionTo('manage users');
        $admin->givePermissionTo('edit articles');

        $moderator = Role::findByName('moderator');
        $moderator->givePermissionTo('edit articles');
    }
}
```

Esegui il seeder:

```bash
php artisan db:seed --class=RoleSeeder
```

### 8. Assegnazione dei Ruoli agli Utenti

Puoi assegnare ruoli agli utenti direttamente nel database o tramite il codice:

```php
// Esempio di assegnazione di un ruolo a un utente
$user = User::find(1);
$user->assignRole('admin');
```

### 9. Middleware per la Gestione dei Ruoli

Crea un middleware per verificare i ruoli degli utenti:

```php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
```

Registra il middleware nel kernel:

```php
// app/Http/Kernel.php

protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

### 10. Protezione delle Rotte

Proteggi le rotte utilizzando il middleware che hai appena creato:

```php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModeratorController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    // Altre rotte per admin
});

Route::middleware(['auth', 'role:moderator'])->group(function () {
    Route::get('/moderator', [ModeratorController::class, 'index']);
    // Altre rotte per moderatori
});
```

### 11. Utilizzo di Inertia.js per le Pagine Protette

Assicurati di passare le informazioni sui ruoli agli utenti nelle tue pagine Vue.js. Ad esempio, nel controller:

```php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'user' => auth()->user(),
        ]);
    }
}
```

E nel componente Vue.js:

```vue
<template>
    <div>
        <h1>Admin Dashboard</h1>
        <p>Benvenuto, {{ user.name }}</p>
    </div>
</template>

<script>
export default {
    props: {
        user: Object,
    },
};
</script>
```

### 12. Visualizzazione Condizionale nei Componenti Vue

Puoi mostrare o nascondere elementi basati sui ruoli dell'utente nel componente Vue:

```vue
<template>
    <div>
        <h1>Dashboard</h1>
        <p>Benvenuto, {{ user.name }}</p>
        <div v-if="user.roles.includes('admin')">
            <p>Questa è una sezione riservata agli admin.</p>
        </div>
        <div v-if="user.roles.includes('moderator')">
            <p>Questa è una sezione riservata ai moderatori.</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: Object,
    },
};
</script>
```
