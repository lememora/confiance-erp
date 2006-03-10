<?php
/**
 * <b><u>Sistema de Gerenciamento Comercial</u></b>
 * <p>Configura��o (config.php)</p>
 * @author Rafael Moraes
 */

// {{{ INCLUDES

ini_set("include_path", 'C:/confiansys/www/confiance/include/;' . ini_get("include_path"));

require './include/Mail.php';
require './include/Smarty/Smarty.class.php';
require './include/Adodb/adodb.inc.php';
require './include/Moraes/data_interface.inc.php';
require './include/SGC/SGC_lib.inc.php';
require './include/SGC/SGC_tabcl.inc.php';
require './include/SGC/SGC_modcl.inc.php';

// }}}

// {{{ CONFIGURA��ES T�PICAS

error_reporting(E_ALL ^ E_NOTICE); // 0|E_ALL ^ E_NOTICE

$CNF_s_base_dir = 'C:/confiansys/www/confiance';
$CNF_s_base_url = 'http://192.168.0.2:8085/confiance/?view=' . @$_REQUEST['view'];
$CNF_s_page_size = 15;

$CNF_smarty_template_dir = $CNF_s_base_dir . '/smarty/kiosk/';
$CNF_smarty_compile_dir = $CNF_s_base_dir . '/smarty/templates_c/';
$CNF_smarty_config_dir = $CNF_s_base_dir . '/smarty/configs/';
$CNF_smarty_cache_dir = $CNF_s_base_dir . '/smarty/cache/';

$CNF_adodb_host = 'localhost';
$CNF_adodb_user = 'root';
$CNF_adodb_pass = '';
$CNF_adodb_db = 'confiance';

// }}}

// {{{ CONSTANTES

/* SMARTY */
define('SMARTY_DIR', $CNF_s_base_dir.'/include/smarty');

/* ADODB */
define('ADODB_DIR', $CNF_s_base_dir.'/include/adodb');

/* VIS�O (VIEW) */
$CT_a_view = array
(
  /* GERAL */
  '_main_' => 'Confiance Brindes',
  '_erro_' => 'Erro',
  '_warn_' => 'Alerta',
  '_info_' => 'Informa��o',
  '_auth_' => 'Autentica��o',
  /* CADASTRO */
  'cadfnc' => 'Fornecedor',
  'cadusr' => 'Usu�rio',
  'cadcli' => 'Cliente',
  'cadprd' => 'Produto',
  'cadcdp' => 'Condi��o de Pagamento',
  'cadctp' => 'Categoria de Pagamento',
  'cadmid' => 'Midia',
  /* FINANCEIRO */
  'finmpc' => 'Pedido de Compra',
  'finpag' => 'Pagamentos',
  'finrec' => 'Recebimentos',
  'finflx' => 'Fluxo de caixa',
  /* VENDAS */
  'venorc' => 'Or�amentos',
  'venped' => 'Pedidos',
  'venlog' => 'Hist�rico',
);

/* MEN� DA VIS�O */
$CT_a_view_menu = array
(
  'Cadastro'   => array ('cadprd','cadcli'),
  'Vendas'     => array ('venorc','venped'),
  //'Financeiro' => array ()
);

/* UF (ESTADOS BRASILEIROS) */
$CT_a_uf = array
(
  'AC' => 'Acre',
  'AL' => 'Alagoas',
  'AP' => 'Amap�',
  'AM' => 'Amazonas',
  'BA' => 'Bahia',
  'CE' => 'Cear�',
  'DF' => 'Distrito Federal',
  'ES' => 'Esp�rito Santo',
  'GO' => 'Goi�s',
  'MA' => 'Maranh�o',
  'MT' => 'Mato Grosso',
  'MS' => 'Mato Grosso do Sul',
  'MG' => 'Minas Gerais',
  'PA' => 'Par�',
  'PB' => 'Para�ba',
  'PR' => 'Paran�',
  'PE' => 'Pernambuco',
  'PI' => 'Piau�',
  'RJ' => 'Rio de Janeiro',
  'RN' => 'Rio Grande do Norte',
  'RS' => 'Rio Grande do Sul',
  'RO' => 'Rond�nia',
  'RR' => 'Roraima',
  'SC' => 'Santa Catarina',
  'SP' => 'S�o Paulo',
  'SE' => 'Sergipe',
  'TO' => 'Tocantins'
);

/* USU�RIOS (tempor�rio) */
$CT_a_tmp_usuarios = array
(
  1 => "ADMIN",
);

/* N�VEL DE SATISFA��O DO CLIENTE */
$CT_a_niv_satisfacao = array
(
  'OTIMO'   => '�timo',
  'BOM'     => 'Bom',
  'REGULAR' => 'Regular',
  'RUIM'    => 'Ruim'
);

/* FORMA DE PAGAMENTO */
$CT_a_forma_pagto = array
(
  'BOLETO'   => 'Boleto',
  'CARTAO'   => 'Cart�o',
  'CHEQUE'   => 'Cheque',
  'DEPOSITO' => 'Dep�sito',
  'DINHEIRO' => 'Dinheiro'
);

/* UNIDADE DE MEDIDA */
$CT_a_un_medida = array
(
  'ITEM'      => 'Item',
  'PE�A'      => 'Pe�a',
  'CONJUNTO'  => 'Conjunto',
  'COR'       => 'Cor',
  'IMPRESSAO' => 'Impress�o',
  'KILOGRAMA' => 'Kilograma',
  'METRO'     => 'Metro',
  'LITRO'     => 'Litro',
);

/* QUANTIDADE DE ATACADO */
$CT_a_qtd_atacado = array
(
  50    => '50',
  100   => '100',
  250   => '250',
  500   => '500',
  1000  => '1.000',
  2000  => '2.000',
  5000  => '5.000',
  10000 => '10.000'
);

$CT_a_month_list = array
(
  1  => "Janeiro",
  2  => "Fevereiro",
  3  => "Mar�o",
  4  => "Abril",
  5  => "Maio",
  6  => "Junho",
  7  => "Julho",
  8  => "Agosto",
  9  => "Setembro",
  10 => "Outubro",
  11 => "Novembro",
  12 => "Dezembro"
);

// }}}

// {{{ OBJETOS

/* SMARTY */
$o_smarty = new Smarty();
$o_smarty->template_dir = $CNF_smarty_template_dir;
$o_smarty->compile_dir = $CNF_smarty_compile_dir;
$o_smarty->config_dir = $CNF_smarty_config_dir;
$o_smarty->cache_dir = $CNF_smarty_cache_dir;
$o_smarty->debugging = FALSE;

/* ADODB */
if(($o_adodb = &ADONewConnection('mysql://'.$CNF_adodb_user.'@'.$CNF_adodb_host.'/'.$CNF_adodb_db))===FALSE)
{
  $o_smarty->assign('ERROR', 'PROBLEMAS AO CONECTAR � BASE DE DADOS!');
  $o_smarty->display('_erro_.tpl.htm'); exit;
}
$o_adodb->debug = FALSE;

// }}}

/* VARI�VEIS SMARTY */
$o_smarty->assign('S_BASE_URL'       , $CNF_s_base_url);
$o_smarty->assign('A_UF'             , $CT_a_uf);
$o_smarty->assign('A_NIV_SATISFACAO' , $CT_a_niv_satisfacao);
$o_smarty->assign('A_FORMA_PAGTO'    , $CT_a_forma_pagto);
$o_smarty->assign('A_UN_MEDIDA'      , $CT_a_un_medida);
$o_smarty->assign('A_QTD_ATACADO'    , $CT_a_qtd_atacado);
$o_smarty->assign('A_VIEW'           , $CT_a_view);
$o_smarty->assign('A_VIEW_MENU'      , $CT_a_view_menu);
$o_smarty->assign('A_USUARIOS'       , $CT_a_tmp_usuarios);

/* SESS�O */
if(session_start()===FALSE)
{
  $o_smarty->assign('ERROR', 'PROBLEMAS AO INICIAR A SESS�O PHP!');
  $o_smarty->display('_erro_.tpl.htm'); exit;
}

/* NAVEGADOR */
if(preg_match("/MSIE [6789]/", $_SERVER['HTTP_USER_AGENT']) == FALSE)
{
  $o_smarty->assign('ERROR', 'REQUERIDO INTERNET EXPLORER VERS�O >= 6.0');
  $o_smarty->display('_erro_.tpl.htm'); exit;
}

?>
