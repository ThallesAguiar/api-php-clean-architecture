<?php

namespace App\Shared\Response;

enum SituacaoEnum: string {
    case SUCCESS = 'success'; //sucesso
    case ERROR   = 'error'; // erro critico
    case ALERT   = 'alert'; // sucesso, mas houve algum problema
}