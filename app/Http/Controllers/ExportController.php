<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ExcelExport;
use App\Models\Instance;

class ExportController extends Controller
{
    public function instances(Excel $excel){
        $this->authorize('admin');

        $headings = [
                        'tombo',
                        'status',
                        'tombo_tipo',
                        'título',
                        'responsabilidades',
                        'assuntos',
                        'editora',
                        'localização',
                        'complemento_localizacao',
                        'isbn',
                        'ano',
                        'ano_que_foi_cadastrado'
                    ];

        $exemplares = Instance::all();
        //$exemplares = Instance::limit(100)->get();
        $data = [];

        foreach($exemplares as $exemplar){

            $responsabilidades = '';
            foreach($exemplar->livro->responsabilidades as $responsabilidade){
                $responsabilidades = $responsabilidade->nome . ';' . $responsabilidades;
            }

            $assuntos= '';
            foreach($exemplar->livro->assuntos as $assunto){
                $assuntos = $assunto->titulo . ';' . $assuntos;
            }

            $data[] = [
                $exemplar->tombo,
                $exemplar->status,
                $exemplar->tombo_tipo,
                $exemplar->livro->titulo,
                $responsabilidades,
                $assuntos,
                $exemplar->livro->editora,
                $exemplar->livro->localizacao,
                $exemplar->livro->complemento_localizacao,
                $exemplar->livro->isbn,
                $exemplar->livro->ano,
                $exemplar->created_at->year
            ];

            // MAIS campos, colocar?
            // $exemplar->notas
            // $exemplar->exemplar
            // $exemplar->livro->local
            // $exemplar->livro->edicao
            // $exemplar->livro->volume
            // $exemplar->livro->obs
            // $exemplar->livro->complemento_localizacao
            // $exemplar->livro->dimensao
            // $exemplar->livro->ilustrado
            // $exemplar->livro->colorido
            // $exemplar->livro->extensao
        }

        $export = new ExcelExport($data,$headings);
        return $excel->download($export, 'exemplares.xlsx');
    }
}
