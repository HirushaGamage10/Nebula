<?php
namespace PHPSTORM_META {
    // Laravel helpers metadata for IDE support
    
    override(\auth(0), type('@App\Models\User'));
    override(\auth(), type('@Illuminate\Contracts\Auth\Guard'));
    override(\view(), type('@Illuminate\View\Factory'));
    override(\response(), type('@Illuminate\Routing\ResponseFactory'));
    override(\redirect(), type('@Illuminate\Routing\Redirector'));
    override(\request(), type('@Illuminate\Http\Request'));
    override(\app(), type('@Illuminate\Contracts\Foundation\Application'));
    override(\config(), type('@Illuminate\Config\Repository'));
    override(\cache(), type('@Illuminate\Cache\CacheManager'));
    override(\session(), type('@Illuminate\Session\SessionManager'));
    override(\event(), type('@Illuminate\Events\Dispatcher'));
    override(\queue(), type('@Illuminate\Queue\QueueManager'));
    override(\storage_path(), type('@string'));
    override(\public_path(), type('@string'));
    override(\resource_path(), type('@string'));
    override(\base_path(), type('@string'));
    override(\database_path(), type('@string'));
}
