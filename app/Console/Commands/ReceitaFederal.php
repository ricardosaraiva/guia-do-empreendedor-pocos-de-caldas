<?php 
namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ReceitaFederal extends Command {
    protected $signature = 'receita';
    protected $description = 'get dados da receita federal';

    public function handle() {
       //acessa o site para cria um cookie
        $cookies = new \GuzzleHttp\Cookie\CookieJar;
        $http =  new Client(['cookies' => true]);
        $http->request('GET','http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/cnpjreva_solicitacao2.asp', 
            ['cookies' => $cookies]
        );
    
        //pega o captcha
        $captcha = $http->request('GET','http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/captcha/gerarCaptcha.asp', [
            'stream' => true,
            'cookies' => $cookies
        ]);

        $body = $captcha->getBody();

        $arquivo = '';
        while (!$body->eof()) {
            $arquivo .= $body->read(1024);
        }

        file_put_contents('captcha.png', $arquivo);

        //requisita o captcha
        $captcha = $this->ask('captcha?');        

        $login = $http->request('POST', 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/valida.asp', [
            'cookies' => $cookies,
            'form_params' => [
                'origem' => 'comprovante',
                'cnpj' => '27865757000102',
                'txtTexto_captcha_serpro_gov_br' => $captcha,
                'submit1' => 'Consultar',
                'search_type' => 'cnpj'
            ]
        ]);
        $dadosLogin = $login->getBody();

        //valida se o login esta valido
        if( !strstr($dadosLogin, 'GLOBO COMUNICACAO E PARTICIPACOES S/A') ) {
            $this->error('Erro ao acessar a pagina!');
            file_put_contents('app/Console/logs/erro.html', $login->getBody());
            exit;
        }

        $dados = $http->request('GET', 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/Cnpjreva_Vstatus.asp?origem=comprovante&cnpj="27865757000102"', [
            'cookies' => $cookies
        ]);

        $parserInformationBasic =  \Helper\ReceitaFederal::parserInformationBasic($dados->getBody()); 

        if($parserInformationBasic != false) {

            $dados = $http->request('GET', 'http://www.receita.fazenda.gov.br/pessoajuridica/cnpj/cnpjreva/Cnpjreva_qsa.asp', [
                'cookies' => $cookies
            ]);

            $parserQsa = \Helper\ReceitaFederal::parserInformationQsa($dados->getBody()); ;    
        }

        
        
        exit;        
    }
}