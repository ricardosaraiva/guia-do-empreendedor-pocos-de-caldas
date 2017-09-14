<?php 
namespace Helper;

class ReceitaFederal {

	public static function parserInformationBasic ( $html ) {
		preg_match_all('/\<table.*?\>.*?\<\/table\>/s', $html, $m);
		
		if(!isset($m[0])) {
			return false;
		}

		$tabelas = $m[0];


		//linha 1 nome, matriz|filial, cnpj, data abertura
		preg_match('/[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}/', $tabelas[2], $matchCnpj);
		preg_match('/MATRIZ|FILIAL/', $tabelas[2], $matchMatrizFilial);
		preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $tabelas[2], $matchDataCadastro);

		//valida o acesso aos dados
		if( !isset($matchCnpj[0]) || mb_strlen(preg_replace('/[^0-9]/' , '', $matchCnpj[0]), 'UTF-8') != 14 || !strstr($html, 'Consulta QSA') ) {
			return false;
		}

		//linha 2 nome da empresa
		preg_match('/\<b\>.*?\<\/b\>/', $tabelas[3], $matchRazao);

		//linha 3 fantasia
		preg_match('/\<b\>.*?\<\/b\>/', $tabelas[4], $matchFantasia);

		//linha 4 atividade principal
		preg_match('/\<b\>.*?\<\/b\>/', $tabelas[5], $matchAtividadePrincipal);

		//linha 5 ativades segundaria
		preg_match_all('/\<b\>.*?\<\/b\>/', $tabelas[6], $matchAtividadeSegundaria);		

		//linha 6 natureza
		preg_match('/\<b\>.*?\<\/b\>/', $tabelas[7], $matchNatureza);

		//linha 7 rua, numero, complemento
		preg_match_all('/\<b\>.*?\<\/b\>/', $tabelas[9], $matchEndereco1);	

		//linha 7 cep, bairro, cidade, uf
		preg_match_all('/\<b\>.*?\<\/b\>/', $tabelas[10], $matchEndereco2);	

		//linha 8 email
		preg_match_all('/\<b\>.*?\<\/b\>/', $tabelas[11], $matchContato);	

		//linha 8 telefone
		preg_match_all('/\([0-9]{2}.*?\-[0-9]{4,5}/', $tabelas[11], $parserTelefone);

		//linha 9 telefone
		preg_match_all('/\<b\>.*?\<\/b\>/', $tabelas[13], $matchSituacao);		


		$parser = isset($matchAtividadePrincipal[0]) ? explode(' - ', trim(strip_tags($matchAtividadePrincipal[0]))) : '';
		$atividadePrincipal = new \StdClass;
		$atividadePrincipal->codigo = isset($parser[0]) ? preg_replace('/[^0-9]/', '', $parser[0]) :  '';
		$atividadePrincipal->descricao = isset($parser[1]) ? $parser[1] :  '';

		$atividadeSegundaria = [];
		if(isset($matchAtividadeSegundaria[0])) {
			foreach ($matchAtividadeSegundaria[0] as $value) {
				$parser = explode(' - ', trim(strip_tags($value)));
				$dados = new \StdClass;
				$dados->codigo = preg_replace('/[^0-9]/', '', $parser[0]);
				$dados->descricao = trim($parser[1]);
				$atividadeSegundaria[] = $dados;
			}
		}

		$parser = isset($matchNatureza[0]) ? explode(' - ', trim(strip_tags($matchNatureza[0]))) : '';
		$natureza = new \StdClass;
		$natureza->codigo = isset($parser[0]) ? preg_replace('/[^0-9]/', '', $parser[0]) :  '';
		$natureza->descricao = isset($parser[1]) ? $parser[1] :  '';

		$telefone = [];
		if(isset($parserTelefone[0])) {
			foreach ($parserTelefone[0] as $value) {
				$telefone[] = preg_replace('/[^0-9]/', '', $value);
			}
		}


		$retorno =  new \StdClass;
		$retorno->cnpj = isset($matchCnpj[0]) ? preg_replace('/[^0-9]/' , '', $matchCnpj[0]) : false;
		$retorno->matrizFilial = isset($matchMatrizFilial[0]) ? $matchMatrizFilial[0] : false;
		$retorno->dataCadastro = isset($matchDataCadastro[0]) ? trim($matchDataCadastro[0]) : false;
		$retorno->razao = isset($matchRazao[0]) ? trim(strip_tags($matchRazao[0])) : false;
		$retorno->fantasia = isset($matchFantasia[0]) ? trim(strip_tags($matchFantasia[0])) : false;
		$retorno->atividadePrincipal = $atividadePrincipal;
		$retorno->atividadeSegundaria = $atividadeSegundaria;
		$retorno->natureza = $natureza;
		$retorno->logradouro = isset($matchEndereco1[0][0]) ? trim(strip_tags($matchEndereco1[0][0])) : false;
		$retorno->numero = isset($matchEndereco1[0][1]) ? trim(strip_tags($matchEndereco1[0][1])) : false;
		$retorno->complemento = isset($matchEndereco1[0][2]) ? trim(strip_tags($matchEndereco1[0][2])) : false;
		$retorno->cep = isset($matchEndereco2[0][0]) ? preg_replace('/[^0-9]/', '', $matchEndereco2[0][0]) : false;
		$retorno->bairro = isset($matchEndereco2[0][0]) ? trim(strip_tags($matchEndereco2[0][1])) : false;
		$retorno->cidade = isset($matchEndereco2[0][0]) ? trim(strip_tags($matchEndereco2[0][2])) : false;
		$retorno->uf = isset($matchEndereco2[0][0]) ? trim(strip_tags($matchEndereco2[0][3])) : false;
		$retorno->email = isset($matchContato[0]) ? trim(strip_tags($matchContato[0][0])) : false;
		$retorno->telefones = $telefone;
		$retorno->situacao = isset($matchSituacao[0][0]) ? trim(strip_tags($matchSituacao[0][0])) : false;

		return $retorno;
	}

	public static function parserInformationQsa( $html ) {
		preg_match('/R\$.*?,[0-9]{2}/', $html, $matchCapitalSocial);
		$qsa = (isset($matchCapitalSocial[0])) ? $matchCapitalSocial[0] : 0;
		return str_replace(',', '.', str_replace('.', '', str_replace('R$', '', $qsa)));
	}

}