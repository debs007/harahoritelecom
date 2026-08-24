<?php
use App\Http\Controllers\Crm\{
    CrmAccessController,
    CrmDashboardController,
    CrmContactController,
    CrmLeadController,
    CrmTicketController,
    CrmCampaignController,
    CrmLoyaltyController,
    CrmTallyController,
    CrmAnalyticsController,
};
use Illuminate\Support\Facades\Route;

// ── CRM Access (no auth required — signature only) ────────────────────────────
Route::prefix('crm')->name('crm.')->group(function () {

    Route::get('/',        [CrmAccessController::class, 'showForm'])->name('access');
    Route::post('/verify', [CrmAccessController::class, 'verify'])->name('verify');
    Route::post('/logout', [CrmAccessController::class, 'logout'])->name('logout');

    // ── All routes below require CRM signature session ────────────────────────
    Route::middleware('crm_auth')->group(function () {

        // Dashboard
        Route::get('/dashboard', [CrmDashboardController::class, 'index'])->name('dashboard');

        // Contacts
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/',                           [CrmContactController::class, 'index'])->name('index');
            Route::post('/',                          [CrmContactController::class, 'store'])->name('store');
            Route::get('/{contact}',                  [CrmContactController::class, 'show'])->name('show');
            Route::put('/{contact}',                  [CrmContactController::class, 'update'])->name('update');
            Route::post('/{contact}/interaction',     [CrmContactController::class, 'addInteraction'])->name('interaction');
            Route::post('/sync-from-orders',          [CrmContactController::class, 'syncFromOrders'])->name('sync');
        });

        // Leads / Sales Pipeline
        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/',                   [CrmLeadController::class, 'index'])->name('index');
            Route::post('/',                  [CrmLeadController::class, 'store'])->name('store');
            Route::put('/{lead}',             [CrmLeadController::class, 'update'])->name('update');
            Route::patch('/{lead}/stage',     [CrmLeadController::class, 'updateStage'])->name('stage');
            Route::delete('/{lead}',          [CrmLeadController::class, 'destroy'])->name('destroy');
        });

        // Tickets / Support
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/',           [CrmTicketController::class, 'index'])->name('index');
            Route::post('/',          [CrmTicketController::class, 'store'])->name('store');
            Route::put('/{ticket}',   [CrmTicketController::class, 'update'])->name('update');
        });

        // Campaigns / Marketing
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/',                        [CrmCampaignController::class, 'index'])->name('index');
            Route::post('/',                       [CrmCampaignController::class, 'store'])->name('store');
            Route::post('/{campaign}/launch',      [CrmCampaignController::class, 'launch'])->name('launch');
            Route::patch('/{campaign}/complete',   [CrmCampaignController::class, 'markComplete'])->name('complete');
        });

        // Loyalty Points
        Route::prefix('loyalty')->name('loyalty.')->group(function () {
            Route::get('/',                            [CrmLoyaltyController::class, 'index'])->name('index');
            Route::post('/adjust/{user}',              [CrmLoyaltyController::class, 'adjust'])->name('adjust');
            Route::get('/notify/{user}',               [CrmLoyaltyController::class, 'sendNotification'])->name('notify');
        });

        // Tally Import
        Route::prefix('tally')->name('tally.')->group(function () {
            Route::get('/',                      [CrmTallyController::class, 'index'])->name('index');
            Route::post('/upload',               [CrmTallyController::class, 'upload'])->name('upload');
            Route::post('/{import}/process',     [CrmTallyController::class, 'process'])->name('process');
            Route::delete('/{import}',           [CrmTallyController::class, 'destroy'])->name('destroy');
        });

        // Analytics
        Route::get('/analytics', [CrmAnalyticsController::class, 'index'])->name('analytics');
    });
});
