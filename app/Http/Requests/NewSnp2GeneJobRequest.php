<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewSnp2GeneJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 'paramsID' => '0'
            //files
            'GWASsummary' => '',
            'leadSNPs' => '',
            'regions' => '',
            'ciMapFile' => '',

            //params
            'addleadSNPs' => '',
            'chrcol' => '',
            'poscol' => '',
            'rsIDcol' => '',
            'pcol' => '',
            'eacol' => '',
            'neacol' => '',
            'orcol' => '',
            'becol' => '',
            'secol' => '',
            'Ncol' => '',
            'egGWAS' => '',
            'N' => '',
            'leadP' => '',
            'gwasP' => '',
            'r2' => '',
            'r2_2' => '',
            'refpanel' => '',
            'refSNPs' => '',
            'maf' => '',
            'mergeDist' => '',
            'posMap' => '',
            'posMapAnnot' => '',
            'posMapAnnoDs' => '',
            'posMapWindow' => '',
            'posMapCADDcheck' => '',
            'posMapCADDth' => '',
            'posMapRDBcheck' => '',
            'posMapRDBth' => '',
            'posMapChr15check' => '',
            'posMapChr15Ts' => '',
            'posMapChr15Max' => '',
            'posMapChr15Meth' => '',
            'posMapAnnoMeth' => '',
            'eqtlMap' => '',
            'eqtlMapTs' => '',
            'sigeqtlCheck' => '',
            'eqtlP' => '',
            'eqtlMapAnnoDs' => '',
            'eqtlMapCADDcheck' => '',
            'eqtlMapCADDth' => '',
            'eqtlMapRDBcheck' => '',
            'eqtlMapRDBth' => '',
            'eqtlMapChr15check' => '',
            'eqtlMapChr15Ts' => '',
            'eqtlMapChr15Max' => '',
            'eqtlMapChr15Meth' => '',
            'eqtlMapAnnoMeth' => '',
            'ciMap' => '',
            'ciMapType' => '',
            'ciMapAnnoDs' => '',
            'ciMapBuiltin' => '',
            'ciFileN' => '',
            'ciMapFDR' => '',
            'ciMapPromWindow' => '',
            'ciMapRoadmap' => '',
            'ciMapEnhFilt' => '',
            'ciMapPromFilt' => '',
            'ciMapCADDcheck' => '',
            'ciMapCADDth' => '',
            'ciMapRDBcheck' => '',
            'ciMapRDBth' => '',
            'ciMapChr15check' => '',
            'ciMapChr15Ts' => '',
            'ciMapChr15Max' => '',
            'ciMapChr15Meth' => '',
            'ciMapAnnoMeth' => '',
            'ensembl' => '',
            'genetype' => '',
            'MHCregion' => '',
            'MHCopt' => '',
            'extMHCregion' => '',
            'magma' => '',
            'magma_window' => '',
            'magma_exp' => '',
            'NewJobTitle' => '',
            // 'SubmitNewJob' => ''
        ];
    }
}
