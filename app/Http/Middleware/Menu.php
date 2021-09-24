<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class Menu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $menu = null;
        if (Auth::check()) {
            $menu = $this->menuUsuario();
            if (Auth::user()->admin == 1) {
                $menu = $this->menuAdmin();
            }
        }
        View::share('menu', $menu);
        return $next($request);
    }

    /**
     * Permite Obtener el menu del usuario
     *
     * @return void
     */
    public function menuUsuario()
    {
        return [

            // Inicio
            'Dashboard' => [
                'submenu' => 0,
                'ruta' => route('home.user'),
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-home',
                'complementoruta' => '',
            ],
            // Fin inicio

            // Market
            'Market' => [
                'submenu' => 0,
                'ruta' => route('shop'),
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-shopping-cart',
                'complementoruta' => '',
            ],
            // Fin Market

            // Negocio
            'Negocio' => [
                'submenu' => 1,
                'ruta' => 'javascripts:;',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-briefcase',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Árbol unilevel',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('genealogy_type', 'tree'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Árbol binario',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('genealogy_type', 'matriz'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Referidos',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('genealogy_list_network', 'direct'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Inversiones',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('inversiones.index'),
                        'complementoruta' => ''
                    ]
                ],
            ],
            // Fin añadir Negocio

            // Financiero
            'Financiero' => [
                'submenu' => 1,
                'ruta' => 'javascripts:;',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-dollar-sign',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Historial de Ordenes',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('shop.orden.history'),
                        'complementoruta' => '',
                    ],
                    [
                        'name' => 'Billetera',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('wallet.index'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Retiros',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('retiro.history'),
                        'complementoruta' => ''
                    ],
                ],
            ],
            // Fin Financiero

            // Soporte
            'Soporte' => [
                'submenu' => 0,
                'ruta' => route('ticket.list-user'),
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-help-circle',
                'complementoruta' => '',
            ],
            // Fin Soporte

        ];

    }

    /**
     * Permite Obtener el menu del admin
     *
     * @return void
     */
    public function menuAdmin()
    {
        return [

            // Inicio
            'Dashboard' => [
                'submenu' => 0,
                'ruta' => route('home'),
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-home',
                'complementoruta' => '',
            ],
            // Fin inicio

            // Inversiones
            'Inversiones' => [
                'submenu' => 1,
                'ruta' => '',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-dollar-sign',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Lista de Inversiones',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('inversiones.index'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Activacion Manual',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('inversiones.activacion'),
                        'complementoruta' => ''
                    ]
                 ],
            ],
            // Fin Inversiones

            // Contabilidad
            'Contabilidad' => [
                'submenu' => 1,
                'ruta' => '',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-clipboard',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Ordenes',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('reports.pedidos'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Rentabilidad',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('reports.rentabilidad'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Rangos',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('audit.rangos'),
                        'complementoruta' => ''
                    ],
                    // [
                    //     'name' => 'Remover Billetera',
                    //     'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                    //     'ruta' => '',
                    //     'complementoruta' => ''
                    // ],
                    [
                        'name' => 'Beneficio Royal',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('reports.beneficio'),
                        'complementoruta' => ''
                    ],
                ]
            ],
            // Fin Contabilidad

            // Auditoria
            'Auditoria' => [
                'submenu' => 1,
                'ruta' => '',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-layout',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Wallet',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' =>  route('wallet.adminWallet'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Historial de puntos',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => '',
                        'complementoruta' => route('audit.puntos')
                    ],
                    [
                        'name' => 'Modificar comisiones',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => '',
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Binario',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => '',
                        'complementoruta' => ''
                    ]
                   
                ]
             ],
            // Fin Auditoria

            // Liquidaciones
            'Retiros' => [
                'submenu' => 1,
                'ruta' => 'javascripts:;',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-pocket',
                'complementoruta' => '',
                'submenus' => [
                    // [
                    //     'name' => 'Por generar',
                    //     'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                    //     'ruta' => route('settlement'),
                    //     'complementoruta' => ''
                    // ],
                    [
                        'name' => 'Solicitud de Retiros',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('settlement.pending'), 
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Historial de Retiros',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('settlement.history'),
                        'complementoruta' => ''
                    ],
                    // [
                    //     'name' => 'Reservados',
                    //     'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                    //     'ruta' => route('settlement.history.status', 'Reservadas'),
                    //     'complementoruta' => ''
                    // ],
                ],
            ],
            // Fin Liquidaciones

            // Retiros
            // 'Retiros' => [
            //     'submenu' => 0,
            //     'ruta' => '',
            //     'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
            //     'icon' => 'feather icon-send',
            //     'complementoruta' => '',
            // ],
            // Fin Retiros

            // Red
            'Red' => [
                'submenu' => 1,
                'ruta' => 'javascripts:;',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-globe',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Usuarios',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('users.list-user'),
                        'complementoruta' => ''
                    ],
                    [
                        'name' => 'Árbol unilevel',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('genealogy_type', 'tree'),
                        'complementoruta' => ''
                    ],
                    // [
                    //     'name' => 'Referidos',
                    //     'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                    //     'ruta' => route('genealogy_list_network', 'direct'),
                    //     'complementoruta' => ''
                    // ],
                ],
            ],
            // Fin Red
            'Crons' => [
                'submenu' => 1,
                'ruta' => 'javascript:;',
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'fa fa-list-alt',
                'complementoruta' => '',
                'submenus' => [
                    [
                        'name' => 'Bono binario',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('wallet.bonoBinario'),
                        'complementoruta' => ''
                    ],
                
                    [
                        'name' => 'Check role',
                        'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                        'ruta' => route('testRank'),
                        'complementoruta' => ''
                    ]

                ]
            ],
            // Usuarios
            // 'Usuarios' => [
            //     'submenu' => 0,
            //     'ruta' => route('users.list-user'),
            //     'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
            //     'icon' => 'feather icon-user',
            //     'complementoruta' => '',
            // ],
            // Fin Usuarios

            // Soporte
            'Soporte' => [
                'submenu' => 0,
                'ruta' => route('ticket.list-admin'),
                'blank'=> '', // si es para una pagina diferente del sistema solo coloquen _blank
                'icon' => 'feather icon-help-circle',
                'complementoruta' => '',
            ],
            // Fin Soporte

        ];
    }
}
