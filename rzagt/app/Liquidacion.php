<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class Liquidacion extends Model

{

    protected $table = "liquidaciones";

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

         'username','user_id', 'fecha', 'comision', 'estado'

    ];



    public function user(){

        return $this->belongsTo('App\User');

    }

}
