<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;
use App\Periodo;
use App\Solicitud;
use Illuminate\Support\Facades\DB;
use App\ConsultaReciboPdf;
use App\Organizacion;
use PDF;




class ConstanciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function solicitudform()
    {
        return view('generarsolicitud');
    }

    public function procesarsolicitud(Request $request)
    {
        $usuario_id = auth()->user()->id; //agregado el 12/07/2019
        $nombresol = auth()->user()->nombre;
        $apellidosol = auth()->user()->apellido;
        $cedulasol = auth()->user()->cedula;
        $emailsol = auth()->user()->email;
        $tiposol = $request->input('tiposol');
        $estatus = 1; //---- estatus 1- por procesar 
        $fecha = date('Y-m-d H:m:s');
       

        // dd($fecha);

        try {

            DB::table('solicitudes')->insert(
                [
                
                'usuario_id' => $usuario_id, //agregado el 12/07/2019
                'nombresol' => $nombresol,
                'apellidosol' => $apellidosol,
                'cedulasol' => $cedulasol,
                'emailsol' => $emailsol,
                'tiposol' => $tiposol,
                'estatus_id' => $estatus,
                'created_at' => $fecha,
                'updated_at' => $fecha
                 
                 ]
            );
           
        } catch (Exception $exception) {
            
            return view('frontend.404');

        }
        // $mensaje = "Tu solicitud fue generada correctamente";
        // dd($mensaje);
        return back()->with('success','Tu solicitud fue generada correctamente');
        // dd('todo listo');
       
    }

    public function listarsolicitudes()
    {
        $sol = Solicitud::all();
        return view('listarsolicitudes', compact('sol'));
        // dd($sol);
    }

    public function actualizarestatus($id)
    {
        $sol = Solicitud::find($id);
        if($sol->estatus_id == 2){
            return back()->with('danger','El estatus ya fue actualizado!');
        }
        
        // dd($sol);
        DB::table('solicitudes')
            ->where('id', $id)
            ->update(['estatus_id' => 2]);
        
        return back()->with('success','El estatus fue actualizado con Exito!');

        
    }

    public function missolicitudes()
    {
        $cedula = auth()->user()->cedula;
        $sol = Solicitud::all()->where('cedulasol', $cedula);
        return view('missolicitudes', compact('sol'));
        dd($sol);
    }




    public function validardatacostancia($usuario_id)
    {
        $usuario = Usuario::find($usuario_id);
        // dd($usuario);
       
        $codper = str_pad($usuario->cedula, 10, "0", STR_PAD_LEFT); 

        // dd($codper);
        $date = date("Y");
        $codemp = Organizacion::All();

        // dd($codper,$date,$codemp);
        $var = new ConsultaReciboPdf(); //clase

        $querypersonalnom = $var->sqlpersonalnomina($codper);
        // dd($querypersonalnom);

        $querynomper = $var->sqlnominaperiodo($codper);
        // dd($querypersonalnom,$querynomper);

        return view('validainfocostancia')->with('querynomper', $querynomper)->with('querypersonalnom', $querypersonalnom)->with('date', $date)->with('codemp', $codemp)->with('codper', $codper);


        // $sol = Solicitud::all()->where('cedulasol', $cedula);
        // return view('missolicitudes', compact('sol'));
        // dd($usuario);
    }

    public function costanciapdf(Request $request)

    {
     
        $codper = $request->input("cedula");
        // dd($codper);
        $codnom = $request->input("codnom");
        $codperi = $request->input("codperi");
        $codemp = $request->input("codemp");
        $date = $request->input("date");

        $query_cabecera =   "SELECT sno_hpersonalnomina.codnom, sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.rifper,
                                MAX(sno_hpersonalnomina.fecingper) as fecingnom, sno_personal.nacper, sno_personal.fecegrper, sno_personal.fecleypen,sno_personal.codorg,
                                sno_hpersonalnomina.obsrecper, sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, sno_personal.fecingper,
                                sum(sno_hsalida.valsal) as total, sno_hunidadadmin.desuniadm, sno_hunidadadmin.minorguniadm,sno_hunidadadmin.ofiuniadm,
                                sno_hunidadadmin.uniuniadm,sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper,
                                MAX(sno_hpersonalnomina.pagbanper) AS pagbanper, MAX(sno_hpersonalnomina.pagefeper) AS pagefeper,
                                MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hpersonalnomina.fecculcontr) AS fecculcontr,
                                MAX(sno_hpersonalnomina.descasicar) AS descasicar,MAX(sno_hpersonalnomina.sueintper) AS sueintper,
                                MAX(sno_hpersonalnomina.sueproper) as sueproper, (SELECT tipnom FROM sno_hnomina WHERE
                                sno_hpersonalnomina.codemp = sno_hnomina.codemp AND sno_hpersonalnomina.codnom = sno_hnomina.codnom AND
                                sno_hpersonalnomina.anocur = sno_hnomina.anocurnom AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom) AS tiponom,
                                (SELECT suemin FROM sno_hclasificacionobrero WHERE sno_hclasificacionobrero.codemp = sno_hpersonalnomina.codemp AND
                                sno_hclasificacionobrero.codnom = sno_hpersonalnomina.codnom AND sno_hclasificacionobrero.anocur = sno_hpersonalnomina.anocur
                                AND sno_hclasificacionobrero.codperi = sno_hpersonalnomina.codperi AND sno_hclasificacionobrero.grado = sno_hpersonalnomina.grado) AS sueobr,
                                (SELECT desest FROM sigesp_estados WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest,
                                (SELECT denmun FROM sigesp_municipio WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai AND sigesp_municipio.codest = sno_ubicacionfisica.codest AND
                                sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, (SELECT denpar FROM sigesp_parroquia WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai AND
                                sigesp_parroquia.codest = sno_ubicacionfisica.codest AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar,
                                (SELECT nomban FROM scb_banco WHERE scb_banco.codemp = sno_hpersonalnomina.codemp AND scb_banco.codban = sno_hpersonalnomina.codban) AS banco, (SELECT nomage
                                FROM scb_agencias WHERE scb_agencias.codemp = sno_hpersonalnomina.codemp AND scb_agencias.codban = sno_hpersonalnomina.codban AND
                                scb_agencias.codage = sno_hpersonalnomina.codage) AS agencia, (SELECT sno_categoria_rango.descat FROM sno_rango, sno_categoria_rango WHERE
                                sno_rango.codemp=sno_personal.codemp AND sno_rango.codcom=sno_personal.codcom AND sno_rango.codran=sno_personal.codran AND
                                sno_categoria_rango.codcat=sno_rango.codcat) AS descat, (SELECT MAX(denestpro2) FROM spg_ep2 WHERE sno_hpersonalnomina.codemp='$codemp'  AND sno_hpersonalnomina.codnom = '$codnom' AND
                                sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm AND
                                sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm AND
                                sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm AND
                                sno_hunidadadmin.codestpro1 = spg_ep2.codestpro1 AND sno_hunidadadmin.codestpro2 = spg_ep2.codestpro2) AS denestpro2,
                                (SELECT codasicar FROM sno_hasignacioncargo WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp AND
                                sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur AND
                                sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as codcar,
                                (SELECT denasicar FROM sno_hasignacioncargo WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp AND
                                sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur AND
                                sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar FROM
                                sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica WHERE sno_hsalida.codemp='0001' AND
                                sno_hsalida.codnom  = '$codnom' AND sno_hsalida.anocur='2019' AND sno_hsalida.codperi='$codperi' AND (sno_hsalida.tipsal<>'P2' AND
                                sno_hsalida.tipsal<>'V4' AND sno_hsalida.tipsal<>'W4') AND sno_hpersonalnomina.codper>='$codper' AND sno_hpersonalnomina.codper<='$codper'AND
                                sno_hsalida.valsal<>0 AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2'
                                OR sno_hsalida.tipsal='W2' OR sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3') AND
                                sno_hpersonalnomina.codemp = sno_hsalida.codemp AND sno_hpersonalnomina.codnom = sno_hsalida.codnom AND
                                sno_hpersonalnomina.anocur = sno_hsalida.anocur AND sno_hpersonalnomina.codperi = sno_hsalida.codperi AND
                                sno_hpersonalnomina.codper = sno_hsalida.codper AND sno_hpersonalnomina.codemp = sno_ubicacionfisica.codemp AND
                                sno_hpersonalnomina.codubifis = sno_ubicacionfisica.codubifis AND sno_hpersonalnomina.codemp = sno_personal.codemp AND
                                sno_hpersonalnomina.codper = sno_personal.codper AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp AND
                                sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur AND
                                sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm
                                AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm
                                AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm
                                GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi,
                                sno_personal.codemp,sno_personal.codcom, sno_personal.codran, sno_personal.codper, sno_personal.cedper, sno_personal.nomper,
                                sno_personal.apeper, sno_personal.rifper, sno_personal.nacper,sno_personal.fecingper, sno_personal.fecegrper, sno_personal.fecleypen,
                                sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, sno_personal.fecingper, sno_hunidadadmin.desuniadm,
                                sno_hpersonalnomina.codasicar, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codban, sno_hunidadadmin.minorguniadm,
                                sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm,sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm,
                                sno_ubicacionfisica.codpai, sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar,
                                sno_hpersonalnomina.codage,sno_personal.codorg,sno_hpersonalnomina.grado, sno_hpersonalnomina.obsrecper,
                                sno_hunidadadmin.codemp,sno_hpersonalnomina.minorguniadm,sno_hpersonalnomina.ofiuniadm,sno_hpersonalnomina.uniuniadm,
                                sno_hpersonalnomina.depuniadm,sno_hpersonalnomina.prouniadm,sno_hunidadadmin.codestpro1,sno_hunidadadmin.codestpro2 ORDER BY
                                sno_personal.codper";
                           
        $query_cabecera = DB::connection('sigesp')->select($query_cabecera);
        $result = $query_cabecera;

        $query_recibo =     "SELECT sno_hconcepto.codconc, sno_hconcepto.titcon as nomcon, sno_hsalida.valsal ,
                                sno_hsalida.tipsal,abs(sno_hconceptopersonal.acuemp) AS acuemp, abs(sno_hconceptopersonal.acupat) 
                                AS acupat,sno_hconcepto.repacucon, sno_hconcepto.repconsunicon, sno_hconcepto.consunicon,
                                (SELECT moncon FROM sno_hconstantepersonal WHERE sno_hconcepto.repconsunicon='1' AND sno_hconstantepersonal.codper = '$codper' AND sno_hconstantepersonal.codemp = sno_hconcepto.codemp AND 
                                sno_hconstantepersonal.codnom = sno_hconcepto.codnom AND sno_hconstantepersonal.anocur = sno_hconcepto.anocur AND sno_hconstantepersonal.codperi = sno_hconcepto.codperi AND 
                                sno_hconstantepersonal.codcons = sno_hconcepto.consunicon ) AS unidad 
                                FROM sno_hsalida, sno_hconcepto, sno_hconceptopersonal 
                                WHERE sno_hsalida.codemp='$codemp' AND sno_hsalida.codnom  = '$codnom' AND sno_hsalida.anocur='$date' AND sno_hsalida.codperi='$codperi' AND sno_hsalida.codper='$codper' AND sno_hsalida.valsal<>0 
                                AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR sno_hsalida.tipsal='P1' OR 
                                sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3') AND sno_hsalida.codemp = sno_hconcepto.codemp AND sno_hsalida.codnom = sno_hconcepto.codnom AND sno_hsalida.anocur = sno_hconcepto.anocur AND
                                sno_hsalida.codperi = sno_hconcepto.codperi AND sno_hsalida.codconc = sno_hconcepto.codconc AND      sno_hsalida.codemp = sno_hconceptopersonal.codemp AND sno_hsalida.codnom = 
                                sno_hconceptopersonal.codnom AND sno_hsalida.anocur = sno_hconceptopersonal.anocur AND sno_hsalida.codperi = sno_hconceptopersonal.codperi 
                                AND sno_hsalida.codconc = sno_hconceptopersonal.codconc AND sno_hsalida.codper = sno_hconceptopersonal.codper 
                                ORDER BY sno_hconcepto.codconc, sno_hsalida.tipsal";

        $query_recibo = DB::connection('sigesp')->select($query_recibo);
        $result2 = $query_recibo;
       
        $prueba2020 = "select * from sno_periodo where codperi = '$codperi' and codnom = '$codnom'";
        
        $prueba2020 = DB::connection('sigesp')->select($prueba2020);

        /*Imprecion del PDF*/

        // $pdf = PDF::loadView('costanciapdfglobal', compact('result', 'result2', 'prueba2020'));
        $pdf = PDF::loadView('costanciapdfglobal', compact('result', 'result2', 'prueba2020', 'query_cabecera', 'query_recibo'));

        return $pdf->download('costanciapdfglobal.pdf');

    }



   }
