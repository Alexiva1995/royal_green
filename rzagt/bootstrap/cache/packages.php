<?php return array (
  'brotzka/laravel-dotenv-editor' => 
  array (
    'providers' => 
    array (
      0 => 'Brotzka\\DotenvEditor\\DotenvEditorServiceProvider',
    ),
    'aliases' => 
    array (
      'DotenvEditor' => 'Brotzka\\DotenvEditor\\DotenvEditorFacade',
    ),
  ),
  'facade/ignition' => 
  array (
    'providers' => 
    array (
      0 => 'Facade\\Ignition\\IgnitionServiceProvider',
    ),
    'aliases' => 
    array (
      'Flare' => 'Facade\\Ignition\\Facades\\Flare',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'hexters/coinpayment' => 
  array (
    'providers' => 
    array (
      0 => 'Hexters\\CoinPayment\\Providers\\CoinPaymentServiceProvider',
    ),
    'aliases' => 
    array (
      'CoinPayment' => 'Hexters\\CoinPayment\\CoinPayment',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'nwidart/laravel-modules' => 
  array (
    'providers' => 
    array (
      0 => 'Nwidart\\Modules\\LaravelModulesServiceProvider',
    ),
    'aliases' => 
    array (
      'Module' => 'Nwidart\\Modules\\Facades\\Module',
    ),
  ),
  'pragmarx/google2fa-laravel' => 
  array (
    'providers' => 
    array (
      0 => 'PragmaRX\\Google2FALaravel\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Google2FA' => 'PragmaRX\\Google2FALaravel\\Facade',
    ),
  ),
  'tightenco/ziggy' => 
  array (
    'providers' => 
    array (
      0 => 'Tightenco\\Ziggy\\ZiggyServiceProvider',
    ),
  ),
);