<?php

namespace App\Http\Controllers;

use App\Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class ProductController extends Controller
{

    /**
     * lleva a la vista del listado de productos
     *
     * @return void
     */
    public function index()
    {
        view()->share('title', 'Productos');
        $products = $this->getProduct();

        return view('product.index', compact('products'));
    }

    /**
     * Permite Traer los productos de la tienda para su modificacion
     *
     * @return void
     */
    public function getProduct()
    {
        $settings = Settings::first();
        $result = DB::table($settings->prefijo_wp.'posts as wp')
                    ->join($settings->prefijo_wp.'postmeta as wpm', 'wp.ID', '=', 'wpm.post_id' )
                    ->where([
                        ['wpm.meta_key', '=', '_price'],
                        ['wp.post_type', '=', 'product'],
                    ])
                    ->select(
                        'wp.ID',
                        'wp.post_title',
                        'wp.post_content',
                        'wpm.meta_value',
                        'wp.post_mime_type as type',
                        'wp.guid as file',
                        'wp.post_excerpt as imagen',
                        'wp.post_password as nivel_pago',
                        'wp.to_ping as porcentaje',
                        'wp.pinged as visible',
                        'wp.post_content_filtered as tipo_pago',
                        'wp.post_parent as dias_activos')
                    ->get();
        foreach ($result as $element) {
            $element->type = json_decode($element->type);
            $element->file = json_decode($element->file);
        }
        return $result;
    }

    public function saveProduct(Request $request)
    {
        $validate = $request->validate([
            'price' => 'required',
            'name' => 'required',
            'content' => 'required'
        ]);

        if ($validate) {
            $settings = Settings::first();
            $fecha = new Carbon();
            $name = str_replace(' ', '-', $request->name);
            $routeFile = [];
            $routeLogo = '';
            $filesTypes = [];
            for ($i=1; $i < 5; $i++) { 
                if (!empty($request->file('file_'.$i))) {
                    $file = $request->file('file_'.$i);
                    $routeFile ['file_'.$i] = $this->fileSave($request['type_file_'.$i], $file, $name.'_'.$i);
                    $filesTypes ['file_'.$i] = $request['type_file_'.$i];
                }
            }
            if (!empty($request->file('imagen'))) {
                $file = $request->file('imagen');
                $routeLogo = $this->fileSave('Logo', $file, 'logo_'.$name);
            }
            if (empty($request->visible)) {
                $request->visible = 'No Visible';
            }
            $id = DB::table($settings->prefijo_wp.'posts')->insertGetId([
                'post_author' => 1,
                'post_date' => $fecha->now(),
                'post_date_gmt' => $fecha->now(),
                'post_content' => $request->content,
                'post_title' => strtoupper($request->name),
                'post_excerpt' => $routeLogo,
                'post_status' => 'publish',
                'comment_status' => 'open',
                'ping_status' => 'closed',
                'post_password' => $request->nivel_pago,
                'post_name' => strtolower($name),
                'to_ping' => ($request->tipo_pago != 'asr') ? $request->porcentaje : 0,
                'pinged' => $request->visible,
                'post_modified' => $fecha->now(),
                'post_modified_gmt' => $fecha->now(),
                'post_content_filtered' => $request->tipo_pago,
                'post_parent' => $request->dias_activos,
                'guid' => json_encode($routeFile),
                'menu_order' => 0,
                'post_type' => 'product',
                'post_mime_type' => json_encode($filesTypes),
                'comment_count' => 0
            ]);
            $this->savePostmetaProduct($id, $request);

            return redirect()->back()->with('msj', 'Producto nuevo agregado');
        }
    }

    /**
     * Permite guardar los archivos y devuelve la ruta para acceder a ella
     *
     * @param string $directory
     * @param array $file
     * @param string $nameProduct
     * @return string
     */
    public function fileSave(string $directory, $file, string $nameProduct) : string
    {
        $namePhoto = Str::slug($nameProduct.''.now()->format('Ymd'), '_');
        $nameExtention = $namePhoto.'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $nameExtention, 'assets');
        $asset_path = ($directory == 'Logo') ? asset('products/'.$path) : $path;
        return $asset_path;
    }

    /**
     * Permite guardar los otros datos del producto
     *
     * @param integer $idproduc
     * @param array $datos
     * @return void
     */
    public function savePostmetaProduct($idproduc, $datos)
    {
        $settings = Settings::first();
        DB::table($settings->prefijo_wp.'postmeta')
            ->insert([
                ['post_id' => $idproduc, 'meta_key' => '_edit_last', 'meta_value' => 1],
                ['post_id' => $idproduc, 'meta_key' => '_edit_lock', 'meta_value' => Carbon::now()->format('ymdhis').':1'],
                ['post_id' => $idproduc, 'meta_key' => '_payment_method', 'meta_value' => $datos['price']],
                ['post_id' => $idproduc, 'meta_key' => 'total_sales', 'meta_value' => 0],
                ['post_id' => $idproduc, 'meta_key' => '_tax_status', 'meta_value' => 'taxable'],
                ['post_id' => $idproduc, 'meta_key' => '_tax_class', 'meta_value' => ''],
                ['post_id' => $idproduc, 'meta_key' => '_manage_stock', 'meta_value' => 'no'],
                ['post_id' => $idproduc, 'meta_key' => '_backorders', 'meta_value' => 'no'],
                ['post_id' => $idproduc, 'meta_key' => '_sold_individually', 'meta_value' => 'no'],
                ['post_id' => $idproduc, 'meta_key' => '_virtual', 'meta_value' => 'no'],
                ['post_id' => $idproduc, 'meta_key' => '_downloadable', 'meta_value' => 'no'],
                ['post_id' => $idproduc, 'meta_key' => '_download_limit', 'meta_value' => -1],
                ['post_id' => $idproduc, 'meta_key' => '_download_expiry', 'meta_value' => -1],
                ['post_id' => $idproduc, 'meta_key' => '_stock', 'meta_value' => NULL],
                ['post_id' => $idproduc, 'meta_key' => '_stock_status', 'meta_value' => 'instock'],
                ['post_id' => $idproduc, 'meta_key' => '_wc_average_rating', 'meta_value' => 0],
                ['post_id' => $idproduc, 'meta_key' => '_wc_review_count', 'meta_value' => 0],
                ['post_id' => $idproduc, 'meta_key' => '_product_version', 'meta_value' => '3.7.0'],
                ['post_id' => $idproduc, 'meta_key' => '_price', 'meta_value' => $datos['price']],
                ['post_id' => $idproduc, 'meta_key' => 'slide_template', 'meta_value' => ''],
            ]);
    }

    /**
     * Permite eliminar los productos del sistema
     *
     * @param integer $id
     * @return void
     */
    public function deleteProduct($id)
    {
        $settings = Settings::first();
        DB::table($settings->prefijo_wp.'posts')->where('ID', $id)->delete();
        DB::table($settings->prefijo_wp.'postmeta')->where('post_id', $id)->delete();

        return redirect()->back()->with('msj', 'Producto borrado sastifactoriamente');
    }


    public function editProduct(Request $request)
    {
        $validate = $request->validate([
            'price' => 'required',
            'name' => 'required',
        ]); 
        
        if ($validate) {
            $settings = Settings::first();
            $fecha = new Carbon();
            $name = str_replace(' ', '-', $request->name);
            $file = DB::table($settings->prefijo_wp.'posts')->where('ID', $request->idproduct)->select('guid', 'post_excerpt', 'post_mime_type')->first();
            $routeFile = json_decode($file->guid);
            $routeLogo = $file->post_excerpt;
            $filesTypes = json_decode($file->post_mime_type);
            for ($i=1; $i < 5; $i++) { 
                if (!empty($request->file('file_'.$i))) {
                    $file = $request->file('file_'.$i);
                    $index = 'file_'.$i;
                    $routeFile->$index = $this->fileSave($request['type_file_'.$i], $file, $name.'_'.$i);
                    $filesTypes->$index = $request['type_file_'.$i];
                }
            }
            if (!empty($request->file('imagen'))) {
                $file = $request->file('imagen');
                $routeLogo = $this->fileSave('Logo', $file, 'logo_'.$name);
            }
            DB::table($settings->prefijo_wp.'posts')->where('ID', $request->idproduct)->update([
                'post_title' => strtoupper($request->name),
                'post_name' => strtolower($name),
                'post_content' => $request->content,
                'post_modified' => $fecha->now(),
                'post_modified_gmt' => $fecha->now(),
                'post_mime_type' => json_encode($filesTypes),
                'guid' => json_encode($routeFile),
                'post_excerpt' => $routeLogo,
                'post_password' => $request->nivel_pago,
                'to_ping' => ($request->tipo_pago != 'asr') ? $request->porcentaje : 0,
                'pinged' => $request->visible,
                'post_content_filtered' => $request->tipo_pago,
                'post_parent' => $request->dias_activos,
            ]);

            $paquete = [
                'nombre' => strtoupper($request->name),
                'ID' => $request->idproduct,
                'monto' => $request->price,
                'nivel' => $request->nivel_pago,
                'porcentaje' => ($request->porcentaje / 100)
            ];
            
            DB::table($settings->prefijo_wp.'postmeta')->where([
                ['post_id', '=', $request->idproduct],
                ['meta_key', '=', '_price']
            ])->update([
                'meta_value' => $request->price
            ]);

            $users = User::all();
            foreach ($users as $user) {
                $userSave = User::find($user->ID);
                if (!empty($userSave->paquete)) {
                    $tmppaquete = json_decode($userSave->paquete);
                    if ($tmppaquete->ID == $request->idproduct) {
                        $userSave->paquete = json_encode($paquete);
                    }
                    $userSave->save();
                }
            }

            return redirect()->back()->with('msj', 'Producto actualizado');
        }
    }

    
}
