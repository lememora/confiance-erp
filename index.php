<?php
/**
 * <b><u>Sistema de Gerenciamento Comercial</u></b>
 * <p>Navegador (index.php)</p>
 * @author Rafael Moraes
 */

require './config.php';

/* PARÂMETROS */
$safe_view      = $CT_a_view[($_REQUEST['view'])] ? $_REQUEST['view'] : '_main_';  // VISÃO
$safe_action    = $_REQUEST['action'] ? $_REQUEST['action'] : 'criar';             // AÇÃO
$safe_page      = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 0;           // PAGINAÇÃO
$safe_sort      = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : NULL;             // ORDERNAR LISTAGEM POR ESTE CAMPO
$safe_show_menu = isset($_REQUEST['show_menu']) ? 1 : 0;                           // EXIBIR MENÚ?

// =============================================================================
// CADASTRO DE FORNECEDORES
// =============================================================================

if($safe_view == 'cadfnc')
{
}

// =============================================================================
// CADASTRO DE USUÁRIOS
// =============================================================================

elseif($safe_view == 'cadusr')
{
}

// =============================================================================
// CADASTRO DE CLIENTES
// =============================================================================

// GET ACTIONS
//
elseif($safe_view == 'cadcli' && $_SERVER['REQUEST_METHOD']=='GET')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'criar' :
    break;

    // -------------------------------------------------------------------------

    case 'listar' :
    $CLI = new Cliente();
    $a_cli = $CLI->listar($CNF_s_page_size,($safe_page*$CNF_s_page_size),$safe_sort);
    $i_cli_total = $CLI->total();
    $o_smarty->assign('PAGES', range(1,ceil($i_cli_total/$CNF_s_page_size)));
    $o_smarty->assign('A_CLIENTES', $a_cli);
    $o_smarty->assign('CLIENTES_TOTAL', $i_cli_total);
    break;

    // -------------------------------------------------------------------------

    case 'buscar'  :
    $CLI = new Cliente();
    $a_cli = $CLI->buscar($_GET['cliente'],$safe_sort);
    $i_cli_total = count($a_cli);

    if($i_cli_total==1)
    {
      $safe_action = 'alterar';
      $o_smarty->assign('A_CLIENTE', $CLI->obter_cliente());
    }
    else
    {
      $o_smarty->assign('PAGES', range(1,ceil($i_cli_total/$CNF_s_page_size)));
      $o_smarty->assign('A_CLIENTES', array_slice($a_cli,($safe_page*$CNF_s_page_size),$CNF_s_page_size));
      $o_smarty->assign('CLIENTES_TOTAL', $i_cli_total);
    }
    break;

    // -------------------------------------------------------------------------

    case 'alterar' :
    $CLI = new Cliente($_GET['id_cliente']);
    $o_smarty->assign('A_CLIENTE', $CLI->obter_cliente());
    break;

    // -------------------------------------------------------------------------

    case 'remover' :
    $CLI = new Cliente($_GET['id_cliente']);
    $_id = $CLI->remover();
    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' REMOVIDO');
    break;

    // -------------------------------------------------------------------------
  }
}
// POST ACTIONS
//
elseif($safe_view == 'cadcli' && $_SERVER['REQUEST_METHOD']=='POST')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'criar'   :
    $CLI = new Cliente();
    $_id = $CLI->gravar($_POST['cliente']);
    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' CRIADO');
    break;

    // -------------------------------------------------------------------------

    case 'alterar' :
    $CLI = new Cliente();
    $_id = $CLI->gravar($_POST['cliente']);
    
    header("Location: ./index.php?view=cadcli&action=alterar&id_cliente=".$_id);
    exit;

    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' ALTERADO');
    break;

    // -------------------------------------------------------------------------
  }
}

// =============================================================================
// CADASTRO DE PRODUTOS
// =============================================================================

// GET ACTIONS
//
elseif($safe_view == 'cadprd' && $_SERVER['REQUEST_METHOD']=='GET')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'criar'   :
    if($_GET['tmp_id'])
    {
      $s_tmp_id = $_GET['tmp_id'];
      $a_data_list = unserialize($_SESSION[$s_tmp_id]);
      $a_data_list['form']=$_GET['produto'];
      $o_smarty->assign('A_PRODUTO', $_GET['produto']);
    }
    else
    {
      $a_data_list = new_prd_data_list();
      $s_tmp_id = gen_tmp_id();
    }
    $_SESSION[$s_tmp_id]=serialize($a_data_list);
    $o_smarty->assign('TMP_ID', $s_tmp_id);
    break;

    // -------------------------------------------------------------------------

    case 'componente' :
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    $_id    = $_GET['_id'] ? str_replace('_','',$_GET['_id']) : NULL;
    $_a_cmp = $_GET['componente'] ? $_GET['componente'] : NULL;
    $_a_tab = $_GET['tabela'] ? $_GET['tabela'] : NULL;

    // adiciona / altera
    if(count($_a_cmp)>0 && count($_a_tab)>0)
    {
      $_a_cmp['nome'] = strtoupper($_a_cmp['nome']);

      if($_id==NULL)
      {
        $a_data_list['componente'][] = array
        (
          'produto_componente' => $_a_cmp,
          'componente_tabela'  => $_a_tab
        );
      }
      else
      {
        $a_data_list['componente'][$_id] = array
        (
          'produto_componente' => $_a_cmp,
          'componente_tabela'  => $_a_tab
        );
      }
    }
    // remove
    if(strlen($_GET['cmpdel'])>1)
    {
      $id = str_replace('_','',$_GET['cmpdel']);
      unset($a_data_list['componente'][$id]);
    }
    // atualiza  quantidade
    if(count($_GET['_prdcmp'])>0)
    {
      foreach($_GET['_prdcmp'] AS $k=>$_a_v)
      if($_a_v['quantidade']!==NULL) $a_data_list['componente'][$k]['produto_componente']['quantidade'] = $_a_v['quantidade'];
    }
    
    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    
    if(strlen($_GET['cmpalt'])>1)
    {
      $id = str_replace('_','',$_GET['cmpalt']);
      $_a_cmp = $a_data_list['componente'][$id]['produto_componente'];
      $_a_cmp['_id'] = $_GET['cmpalt'];
      $o_smarty->assign('A_COMPONENTE',$_a_cmp);
      $o_smarty->assign('A_TABELA',$a_data_list['componente'][$id]['componente_tabela']);
    }
    else
    {
      $o_smarty->assign('A_TABELA',new_cmp_data_list());
    }
    
    $o_smarty->assign('A_PRODUTO_COMPONENTES', $a_data_list['componente']);
    break;

    // -------------------------------------------------------------------------

    case 'custo' :
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    // atualiza
    if($_GET['custo']) $a_data_list['custo'] = $_GET['custo'];

    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('A_PRODUTO_CUSTOS', $a_data_list['custo']);
    break;

    // -------------------------------------------------------------------------

    case 'ppv' :
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    // altera itens
    if(count($_GET['ppv'])>0) $a_data_list['ppv']=$_GET['ppv'];

    // opção de CALCULO.
    if($_GET['calculo']) $a_data_list['calculo']=$_GET['calculo'];

    // CALCULO dos valores do produto
    //
    $PRD =& new Produto();
    $_a_calc_res = $PRD->calc_ppv($a_data_list['form'],
                                  $a_data_list['componente'],
                                  $a_data_list['custo'],
                                  $a_data_list['ppv']);
                                  
    foreach((array)$a_data_list['ppv'] AS $k=>$_a_ppv)
    {
      $a_data_list['ppv'][$k]['venda_un'] = number_format(round($_a_calc_res[$k],3),3);
      $a_data_list['ppv'][$k]['_total'] = number_format(round(($_a_calc_res[$k]*$a_data_list['ppv'][$k]['qtd_atacado']),2),2);
    }

    usort($a_data_list['ppv'],'ppv_sort');
    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('PPV' , $a_data_list['ppv']);
    $o_smarty->assign('CALCULO' , $a_data_list['calculo']);
    break;

    // -------------------------------------------------------------------------

    case 'listar'  :
    $PRD = new Produto();
    $a_prd = $PRD->listar($CNF_s_page_size,($safe_page*$CNF_s_page_size),$safe_sort);
    $i_prd_total = $PRD->total();
    $o_smarty->assign('PAGES', range(1,ceil($i_prd_total/$CNF_s_page_size)));
    $o_smarty->assign('A_PRODUTOS', $a_prd);
    $o_smarty->assign('PRODUTOS_TOTAL', $i_prd_total);
    break;

    // -------------------------------------------------------------------------

    case 'buscar'  :
    $PRD = new Produto();
    $a_prd = $PRD->buscar($_GET['produto'],$safe_sort);
    $i_prd_total = count($a_prd);

    if($i_prd_total==1)
    {
      $safe_action = 'alterar';
      $PRD = new Produto($a_prd[0]['id_produto']);
    
      $a_data_list['form']       = $PRD->obter_produto();
      $a_data_list['componente'] = $PRD->obter_componente();
      $a_data_list['custo']      = $PRD->obter_custo();
      $a_data_list['ppv']        = $PRD->obter_ppv();
      $s_tmp_id = gen_tmp_id();

      $_SESSION[$s_tmp_id]=serialize($a_data_list);
      $o_smarty->assign('A_PRODUTO', $a_data_list['form']);
      $o_smarty->assign('TMP_ID', $s_tmp_id);
    }
    else
    {
      $o_smarty->assign('PAGES', range(1,ceil($i_prd_total/$CNF_s_page_size)));
      $o_smarty->assign('A_PRODUTOS', array_slice($a_prd,($safe_page*$CNF_s_page_size),$CNF_s_page_size));
      $o_smarty->assign('PRODUTOS_TOTAL', $i_prd_total);
    }
    break;

    // -------------------------------------------------------------------------

    case 'alterar' :
    if($_GET['id_produto']) $PRD = new Produto($_GET['id_produto']);
    elseif($_GET['produto']['id_produto']) $PRD = new Produto($_GET['produto']['id_produto']);

    $_a_data_list = unserialize($_SESSION[($_GET['tmp_id'])]);
    if(is_array($_a_data_list))
    {
      $a_data_list = $_a_data_list;
      $s_tmp_id = $_GET['tmp_id'];
    }
    else
    {
      $a_data_list['componente'] = $PRD->obter_componente();
      $a_data_list['custo']      = $PRD->obter_custo();
      $a_data_list['ppv']        = $PRD->obter_ppv();
      $s_tmp_id = gen_tmp_id();
    }

    if($_GET['produto']) $a_data_list['form']=$_GET['produto'];
    else $a_data_list['form']=$PRD->obter_produto();

    $_SESSION[$s_tmp_id]=serialize($a_data_list);
    $o_smarty->assign('A_PRODUTO', $a_data_list['form']);
    $o_smarty->assign('TMP_ID', $s_tmp_id);
    break;

    // -------------------------------------------------------------------------

    case 'remover' :
    $PRD = new Produto($_GET['id_produto']);
    $_id = $PRD->remover();
    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' REMOVIDO');
    break;

    // -------------------------------------------------------------------------
    
    case 'tab' :
    $PRD = new Produto();
    $a_prd = $PRD->o_produto->di_all(array(),'referencia');
    
    //$output  = "<input type='button' value='IMPRIMIR' OnClick='printpage()'>\n";
    $output  = "PARA IMPRIMIR, CLIQUE COM O BOTÃO DIREITO E DEPOIS 'Imprimir' ou 'Print'<br><br>\n";
    $output .= "<table border=0 cellpadding=0 cellspacing=1 width=650><tr>";
    $output .= "<td class=textoform><b>REF</b></td>";
    $output .= "<td class=textoform><b>DESCRICAO</b></td>";
    $output .= "<td class=textoform><b>50</b></td>";
    $output .= "<td class=textoform><b>100</b></td>";
    $output .= "<td class=textoform><b>250</b></td>";
    $output .= "<td class=textoform><b>500</b></td>";
    $output .= "<td class=textoform><b>1000</b></td>";
    $output .= "<td class=textoform><b>2000</b></td>";
    $output .= "<td class=textoform><b>5000</b></td>";
    $output .= "<td class=textoform><b>10000</b></td></tr>\n";
    
    for($i=0;$i<count($a_prd);$i++)
    {
        $_PRD = new Produto($a_prd[$i]['id_produto']);
        
        $_a_calc_res = $_PRD->calc_ppv($a_prd[$i],
                                       $_PRD->obter_componente(),
                                       $_PRD->obter_custo(),
                                       $_PRD->obter_ppv());
        
        $output .= "<tr>";
        $output .= "<td class=mintextoform nowrap><b>". $a_prd[$i]['referencia'] . "</b>&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform>". $a_prd[$i]['descricao'] . "&nbsp;&nbsp;</td>";
        
        if($_a_calc_res[0]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[0],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[0],'2',',','.') . "&nbsp;&nbsp;</td>";

        if($_a_calc_res[1]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[1],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[1],'2',',','.') . "&nbsp;&nbsp;</td>";
        
        if($_a_calc_res[2]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[2],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[2],'2',',','.') . "&nbsp;&nbsp;</td>";
        
        if($_a_calc_res[3]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[3],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[3],'2',',','.') . "&nbsp;&nbsp;</td>";

        if($_a_calc_res[4]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[4],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[4],'2',',','.') . "&nbsp;&nbsp;</td>";

        if($_a_calc_res[5]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[5],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[5],'2',',','.') . "&nbsp;&nbsp;</td>";

        if($_a_calc_res[6]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[6],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[6],'2',',','.') . "&nbsp;&nbsp;</td>";

        if($_a_calc_res[7]==0)
        $output .= "<td class=textoform align=right><b><u><i>". number_format($_a_calc_res[7],'2',',','.') . "</i></u></b>&nbsp;&nbsp;</td>";
        else
        $output .= "<td class=textoform align=right>". number_format($_a_calc_res[7],'2',',','.') . "&nbsp;&nbsp;</td>";
        
        $output .= "</tr>\n";
        
    }
    $output .= "</table>\n";
    // $output .= "<script language='JavaScript'>printpage();</script>\n";
    $o_smarty->assign('PRDTABLE', $output);
    break;
    
    // -------------------------------------------------------------------------
  }
}
// POST ACTIONS
//
elseif($safe_view == 'cadprd' && $_SERVER['REQUEST_METHOD']=='POST')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------
    
    case 'criar'   :
    $a_data_list = unserialize($_SESSION[($_POST['tmp_id'])]);
    $a_produto_cmp = $a_data_list['componente'];
    $a_produto_cst = $a_data_list['custo'];
    $a_produto_ppv = $a_data_list['ppv'];

    $PRD = new Produto();
    $_id = $PRD->gravar($_POST['produto'],$a_produto_cmp,$a_produto_cst,$a_produto_ppv);
    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' CRIADO');
    break;

    // -------------------------------------------------------------------------
    
    case 'alterar'   :
    $a_data_list = unserialize($_SESSION[($_POST['tmp_id'])]);
    $a_produto_cmp = $a_data_list['componente'];
    $a_produto_cst = $a_data_list['custo'];
    $a_produto_ppv = $a_data_list['ppv'];

    $PRD = new Produto();
    $_id = $PRD->gravar($_POST['produto'],$a_produto_cmp,$a_produto_cst,$a_produto_ppv);
    
    header("Location: ./index.php?view=cadprd&action=alterar&id_produto=".$_id);
    exit;

    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' ALTERADO');
    break;


    // -------------------------------------------------------------------------
  }
}

// =============================================================================
// CADASTRO DE CONDIÇÕES DE PAGAMENTO
// =============================================================================

elseif($safe_view == 'cadcdp')
{
}

// =============================================================================
// CADASTRO DE CATEGORIAS DE PAGAMENTO
// =============================================================================

elseif($safe_view == 'cadctp')
{
}

// =============================================================================
// CADASTRO DE MÍDIAS
// =============================================================================

elseif($safe_view == 'cadmid')
{
}

// =============================================================================
// PEDIDOS DE COMPRA
// =============================================================================

elseif($safe_view == 'finmpc')
{
}

// =============================================================================
// PAGAMENTOS
// =============================================================================

elseif($safe_view == 'finpag')
{
}

// =============================================================================
// RECEBIMENTOS
// =============================================================================

elseif($safe_view == 'finrec')
{
}

// =============================================================================
// FLUXO DE CAIXA
// =============================================================================

elseif($safe_view == 'finflx')
{
}

// =============================================================================
// ORÇAMENTOS
// =============================================================================

// GET ACTIONS
//
elseif($safe_view == 'venorc' && $_SERVER['REQUEST_METHOD']=='GET')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'criar' :

    if($_GET['tmp_id'])
    {
      $s_tmp_id = $_GET['tmp_id'];
      $a_data_list = unserialize($_SESSION[$s_tmp_id]);
      $a_data_list['form']=$_GET['orcamento'];
      $a_data_list['form']['id_cliente']=$_GET['__id_cliente'];
      $CLI = new Cliente($_GET['__id_cliente']);
      $o_smarty->assign('A_CLIENTE', $CLI->obter_cliente());
      $o_smarty->assign('A_ORCAMENTO', $a_data_list['form']);
    }
    else
    {
      $a_data_list = new_orc_data_list();
      $s_tmp_id = gen_tmp_id();
    }
    
    $MID = new Midia();
    $o_smarty->assign('A_MIDIAS', $MID->listar_tudo());
    $CDP = new CondPagto();
    $o_smarty->assign('A_COND_PAGTO', $CDP->listar_tudo());
    $_SESSION[$s_tmp_id]=serialize($a_data_list);
    $o_smarty->assign('TMP_ID', $s_tmp_id);
    break;

    // -------------------------------------------------------------------------

    case 'clientes' :
    if($_GET['cliente'])
    {
      $CLI = new Cliente();
      $o_smarty->assign('A_CLIENTES',$CLI->buscar($_GET['cliente'],'fantasia','razao_social'));
    }
    break;

    // -------------------------------------------------------------------------

    case 'produtos' :     // a parte de adicionar / alterar está no post  por limitacoes do get
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    // remove
    if($_GET['keydel']!==NULL)
    {
      unset($a_data_list['produto'][($_GET['keydel'])]);
    }

    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('APROVADO', $a_data_list['form']['aprovado']);
    $o_smarty->assign('A_ORCAMENTO_PRODUTOS', $a_data_list['produto']);
    break;

    // -------------------------------------------------------------------------

    case 'componentes' :
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    // atualiza quantidades ..
    if(count($_GET['_prdcmp'])>0)
    {
      foreach($_GET['_prdcmp'] AS $k=>$_a_v)
      $a_data_list['produto'][str_replace('_','',$_GET['prdkey'])]['cmp'][$k]['produto_componente']['quantidade'] = $_a_v['quantidade'];
    }

    $_a_prd = $a_data_list['produto'][str_replace('_','',$_GET['prdkey'])];

    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('PRD_CMP', $_a_prd['cmp']);
    $o_smarty->assign('PRD_DESCRICAO', $_a_prd['inf']['descricao']);
    break;

    // -------------------------------------------------------------------------

    case 'opv' :
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    // altera itens
    if(count($_GET['opv'])>0) $a_data_list['opv']=$_GET['opv'];

    // CALCULO dos valores do produto
    //
    $ORC =& new Orcamento();
    $_a_calc_res = $ORC->calc_opv($a_data_list['form'],
                                  $a_data_list['produto'],
                                  $a_data_list['opv']);

    foreach((array)$a_data_list['opv'] AS $k=>$_a_opv)
    {
      $qtd = $_a_opv['qtd_atacado'];
      $a_data_list['opv'][$k]['venda_un'] = number_format(round($_a_calc_res[$qtd],3),3);
      $a_data_list['opv'][$k]['_total'] = number_format(round(($_a_calc_res[$qtd]*$qtd),2),2);
    }

    usort($a_data_list['opv'],'ppv_sort');
    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('OPV' , $a_data_list['opv']);
    break;

    // -------------------------------------------------------------------------

    case 'listar' :
    $ORC = new Orcamento();
    $safe_sort = $safe_sort ? $safe_sort : 'id_orcamento desc';
    $a_orc = $ORC->listar($CNF_s_page_size,($safe_page*$CNF_s_page_size),$safe_sort);
    $i_orc_total = $ORC->total();
    # GAMBIARRA P PEGAR FANTASIA
    foreach((array)$a_orc AS $k=>$v)
    {
      $_CLI = new Cliente($v['id_cliente']);
      $_a_cli = $_CLI->obter_cliente();
      $a_orc[$k]['_cliente_fantasia'] = $_a_cli['fantasia'];
    }
    # GAMBIARRA P FORMATAR DATA
    foreach($a_orc AS $_aorck=>$aorcv)
    $a_orc[$_aorck]['dt_emissao'] = date2data($a_orc[$_aorck]['dt_emissao']);

    $o_smarty->assign('PAGES', range(1,ceil($i_orc_total/$CNF_s_page_size)));
    $o_smarty->assign('A_ORCAMENTOS', $a_orc);
    $o_smarty->assign('ORCAMENTOS_TOTAL', $i_orc_total);
    break;

    // -------------------------------------------------------------------------

    case 'pesquisar' :
    $MID = new Midia();
    $o_smarty->assign('A_MIDIAS', $MID->listar_tudo());
    $CDP = new CondPagto();
    $o_smarty->assign('A_COND_PAGTO', $CDP->listar_tudo());
    break;
    
    // -------------------------------------------------------------------------

    case 'buscar'  :
    $ORC = new Orcamento();
    $_a_busca = $_GET['orcamento'];
    if($_GET['__id_cliente']) $_a_busca['id_cliente'] = $_GET['__id_cliente'];

    $a_orc = $ORC->buscar($_a_busca,$safe_sort);
    $i_orc_total = count($a_orc);

    if($i_orc_total==1)
    {
      $safe_action = 'alterar';
      
      // ----------- AQUI E IGUAL O ALTERAR ----
      
      $a_data_list = array();
      $ORC = new Orcamento($a_orc[0]['id_orcamento']);
      $a_data_list['form']=$ORC->obter_orcamento();

      # CARREGA TODOS OS OPCS  DO ORCAMENTO .
      $___opc = new _orcamento_prd_cmp();
      $_a_opc = array();
      foreach((array)$___opc->di_all(array('id_orcamento'=>$a_orc[0]['id_orcamento'])) AS $_opck=>$_opcv)
      $_a_opc[($_opcv['id_produto'])][($_opcv['id_componente'])] = $_opcv['quantidade'];

      $_last_id_prd = 0;
      $_last_qtdata = 0;
      foreach((array)$ORC->obter_opv() AS $_a)
      {
        if($_last_id_prd != $_a['id_produto'] || $_a['qtd_atacado']<$_last_qtdata)
        {
          $_PRD = new Produto($_a['id_produto']);
          $_a_prd = $_PRD->obter_produto();
          $prd_tmp_id = gen_tmp_id();
          $a_data_list['produto'][$prd_tmp_id]['inf'] = $_a;
          $a_data_list['produto'][$prd_tmp_id]['inf']['referencia'] = $_a_prd['referencia'];
          $a_data_list['produto'][$prd_tmp_id]['inf']['descricao'] = $_a_prd['descricao'];
          $_last_id_prd = $_a['id_produto']; # permite varias intancias de um mesmo produto
        }
        $_last_qtdata = $_a['qtd_atacado']; # permite varias intancias de um mesmo produto
        $a_data_list['produto'][$prd_tmp_id]['qtd'][($_a['qtd_atacado'])]=$_a['venda_un'];

        # OBTEM OS COMPONENTES DOS PRODUTOS DO ORCAMENTO E ATRIBUI OS SEUS RESPECTIVOS OPCS.;.
        $_a_cmp = $_PRD->obter_componente();
        foreach($_a_cmp AS $_cmpk=>$_cmpv)
        {
          $_a_cmp[$_cmpk]['produto_componente']['quantidade'] = $_a_opc[($_cmpv['produto_componente']['id_produto'])][($_cmpv['produto_componente']['id_componente'])];
        }
        $a_data_list['produto'][$prd_tmp_id]['cmp'] = $_a_cmp;
      }
      $s_tmp_id = gen_tmp_id();


      $_CLI = new Cliente($a_data_list['form']['id_cliente']);
      $o_smarty->assign('A_CLIENTE', $_CLI->obter_cliente());
      $_SESSION[$s_tmp_id]=serialize($a_data_list);
      $MID = new Midia();
      $o_smarty->assign('A_MIDIAS', $MID->listar_tudo());
      $CDP = new CondPagto();
      $o_smarty->assign('A_COND_PAGTO', $CDP->listar_tudo());
      $o_smarty->assign('A_ORCAMENTO', $a_data_list['form']);
      $o_smarty->assign('TMP_ID', $s_tmp_id);

      // ----------- AQUI E IGUAL O ALTERAR ----
      
    }
    else
    {
      # GAMBIARRA P PEGAR FANTASIA
      foreach((array)$a_orc AS $k=>$v)
      {
        $_CLI = new Cliente($v['id_cliente']);
        $_a_cli = $_CLI->obter_cliente();
        $a_orc[$k]['_cliente_fantasia'] = $_a_cli['fantasia'];
      }
      # GAMBIARRA P FORMATAR DATA
      foreach($a_orc AS $_aorck=>$aorcv)
      $a_orc[$_aorck]['dt_emissao'] = date2data($a_orc[$_aorck]['dt_emissao']);
      
      $o_smarty->assign('PAGES', range(1,ceil($i_orc_total/$CNF_s_page_size)));
      $o_smarty->assign('A_ORCAMENTOS', array_slice($a_orc,($safe_page*$CNF_s_page_size),$CNF_s_page_size));
      $o_smarty->assign('ORCAMENTOS_TOTAL', $i_orc_total);
    }
    break;

    // -------------------------------------------------------------------------

    case 'alterar' :
    if($_GET['id_orcamento']) $ORC = new Orcamento($_GET['id_orcamento']);
    elseif($_GET['orcamento']['id_orcamento']) $ORC = new Orcamento($_GET['orcamento']['id_orcamento']);

    $_a_data_list = unserialize($_SESSION[($_GET['tmp_id'])]);
    if(is_array($_a_data_list))
    {
      $a_data_list = $_a_data_list;
      $s_tmp_id = $_GET['tmp_id'];
    }
    else
    {
      # CARREGA TODOS OS OPCS  DO ORCAMENTO .
      $___opc = new _orcamento_prd_cmp();
      $_a_opc = array();
      foreach((array)$___opc->di_all(array('id_orcamento'=>$_GET['id_orcamento'])) AS $_opck=>$_opcv)
      $_a_opc[($_opcv['id_produto'])][($_opcv['id_componente'])] = $_opcv['quantidade'];

      $_last_id_prd = 0;
      $_last_qtdata = 0;
      
#
#      $__current_product = NULL;
#
#      #### GERA ARRAY COM PRODUTOSSSSS FILHO DA PUTA DO CARALHO ...
#      foreach((array)$ORC->obter_opv() AS $_a)
#      {
#echo $_a['qtd_atacado'] . "<br>\n";
#      }
#
#
#
      foreach((array)$ORC->obter_opv() AS $_a)
      {
        if($_last_id_prd != $_a['id_produto'] || $_a['qtd_atacado']<$_last_qtdata)
        {
          $_PRD = new Produto($_a['id_produto']);
          $_a_prd = $_PRD->obter_produto();
          $prd_tmp_id = gen_tmp_id();
          $a_data_list['produto'][$prd_tmp_id]['inf'] = $_a;
          $a_data_list['produto'][$prd_tmp_id]['inf']['referencia'] = $_a_prd['referencia'];
          $a_data_list['produto'][$prd_tmp_id]['inf']['descricao'] = $_a_prd['descricao'];
          $_last_id_prd = $_a['id_produto'];  # permite varias instancias de um mesmo produto
        }
        $_last_qtdata = $_a['qtd_atacado']; # permite varias intancias de um mesmo produto
        $a_data_list['produto'][$prd_tmp_id]['qtd'][($_a['qtd_atacado'])]=$_a['venda_un'];

        # OBTEM OS COMPONENTES DOS PRODUTOS DO ORCAMENTO E ATRIBUI OS SEUS RESPECTIVOS OPCS.;.
        $_a_cmp = $_PRD->obter_componente();
        foreach($_a_cmp AS $_cmpk=>$_cmpv)
        {
          $_a_cmp[$_cmpk]['produto_componente']['quantidade'] = $_a_opc[($_cmpv['produto_componente']['id_produto'])][($_cmpv['produto_componente']['id_componente'])];
        }
        $a_data_list['produto'][$prd_tmp_id]['cmp'] = $_a_cmp;
      }

      $s_tmp_id = gen_tmp_id();
    }

    if($_GET['orcamento'])
    {
      $a_data_list['form']=$_GET['orcamento'];
      $a_data_list['form']['id_cliente']=$_GET['__id_cliente'];
    }
    else
    {
      $a_data_list['form']=$ORC->obter_orcamento();
    }

    $_CLI = new Cliente($a_data_list['form']['id_cliente']);
    $o_smarty->assign('A_CLIENTE', $_CLI->obter_cliente());
    $_SESSION[$s_tmp_id]=serialize($a_data_list);
    $MID = new Midia();
    $o_smarty->assign('A_MIDIAS', $MID->listar_tudo());
    $CDP = new CondPagto();
    $o_smarty->assign('A_COND_PAGTO', $CDP->listar_tudo());
    $o_smarty->assign('A_ORCAMENTO', $a_data_list['form']);
    $o_smarty->assign('TMP_ID', $s_tmp_id);

    break;

    // -------------------------------------------------------------------------

    case 'remover' :
    $ORC = new Orcamento($_GET['id_orcamento']);
    $_id = $ORC->remover();
    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' REMOVIDO');
    break;

    // -------------------------------------------------------------------------

    case 'email' :
    $a_data_list = unserialize($_SESSION[($_GET['tmp_id'])]);
    $_CLI = new Cliente($a_data_list['form']['id_cliente']);
    $_a_cli = $_CLI->obter_cliente();
    $_CPG = new CondPagto($a_data_list['form']['id_cond_pagto']);
    $_a_cpg = $_CPG->obter_cond_pagto();

    $a_mail_data = array();

    $a_mail_data['DIA'] = date('d');
    $a_mail_data['MES'] = $CT_a_month_list[date('n')];
    $a_mail_data['ANO'] = date('Y');
    $a_mail_data['ID'] = $a_data_list['form']['id_orcamento'];
    $a_mail_data['OBSERVACAO'] = $a_data_list['form']['observacao'];
    $a_mail_data['FANTASIA'] = $_a_cli['fantasia'];
    $a_mail_data['CNPJ'] = $_a_cli['cnpj_cpf'];
    $a_mail_data['IE'] = $_a_cli['ie_rg'];
    $a_mail_data['CEP'] = $_a_cli['cep'];
    $a_mail_data['RAZAO_SOCIAL'] = $_a_cli['razao_social'];
    if($_a_cli['endereco']) $a_mail_data['ENDERECO'] = $_a_cli['endereco'];
    if($_a_cli['bairro'])   $a_mail_data['ENDERECO'].= " - ". $_a_cli['bairro'];
    if($_a_cli['cidade'])   $a_mail_data['ENDERECO'].= " - ". $_a_cli['cidade'];
    if($_a_cli['uf'])       $a_mail_data['ENDERECO'].= " - ". $_a_cli['uf'];;
    $a_mail_data['CONTATO'] = $_a_cli['contato'];
    $a_mail_data['TEL'] = $_a_cli['telefone_1'];
    $a_mail_data['FAX'] = $_a_cli['telefone_fax'];
    $a_mail_data['EMAIL'] = $_a_cli['email'];

    foreach((array)$a_data_list['produto'] AS $_id=>$_ap)
    {
      $a_mail_data['TABELA'][$_id]['referencia'] = $_ap['inf']['referencia'];
      $a_mail_data['TABELA'][$_id]['descricao'] = $_ap['inf']['descricao'];
      $a_mail_data['TABELA'][$_id]['valor'] = $_ap['qtd'];
    }

    $a_mail_data['COND_PGTO'] = $_a_cpg['nome'];
    $a_mail_data['PRAZO_ENTREGA'] = $a_data_list['form']['prazo_entrega'];
    $a_mail_data['FOTOLITO'] = $a_data_list['form']['custo_base'];
    $a_mail_data['GRAVACAO'] = $a_data_list['form']['gravacao'];
    $a_mail_data['COR'] = $a_data_list['form']['cor'];
    $a_mail_data['REPRESENTANTE'] = $CT_a_tmp_usuarios[($a_data_list['form']['id_usuario'])];
    $a_mail_data['EMAIL_VENDEDOR'] = 'vendas@confiancebrindes.com.br';

    $s_tmp_id = gen_tmp_id();
    $_SESSION[$s_tmp_id] = serialize($a_mail_data);
    $o_smarty->assign('TMP_ID', $s_tmp_id);
    $o_smarty->assign('MAIL_DATA', $a_mail_data);

    break;

    // -------------------------------------------------------------------------
    
    case 'viseml' :
    echo "<input type=button value=imprimir OnClick='window.print()'><br>\n";
    $a_mail_data = unserialize($_SESSION[($_GET['tmp_id'])]);
    $o_smarty->assign($a_mail_data);
    #echo "<pre>";
    #print_r($a_mail_data);
    #echo "</pre>";
    $o_smarty->display('_email.tpl.htm'); exit;
    break;
    
    // -------------------------------------------------------------------------
    
    case 'gotoped' :
    $PED = new Pedido();
    $_a_ped = $PED->obter_ped_por_orc($_GET['id_orcamento']);
    header("Location: ./index.php?view=venped&action=alterar&id_pedido=".$_a_ped['id_pedido']);
    exit;
    break;
    
    // -------------------------------------------------------------------------
  }
}
// POST ACTIONS
//
elseif($safe_view == 'venorc' && $_SERVER['REQUEST_METHOD']=='POST')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'produtos' : // a parte de del esta no get
    if($_POST['tmp_id']) $a_data_list=unserialize($_SESSION[($_POST['tmp_id'])]);

    $PRD=new Produto();
    $_ref = $_POST['orcamento']['add']['referencia'];
    unset($_POST['orcamento']['add']);
    
    #// REMOVE PRODUTOS DUPLICADOS   // PODE NA BOA ! ops...
    #foreach((array)$a_data_list['produto'] AS $k=>$_a_v)
    #if(strtoupper($_ref) == strtoupper($_a_v['inf']['referencia'])) unset($_ref);

    // atualiza
    foreach((array)$_POST['orcamento'] AS $k=>$_a_v)
    {
      if(count($_a_v['inf'])>0) $a_data_list['produto'][$k]['inf']=$_a_v['inf'];
      if(count($_a_v['qtd'])>0) $a_data_list['produto'][$k]['qtd']=$_a_v['qtd'];
    }

    // recalcula
    if($_POST['recalc']==1)
    {
      foreach((array)$a_data_list['produto'] AS $__k=>$_a_v)
      {
        $__ref = $_a_v['inf']['referencia'];
        $PRD->obter_prd_por_ref($__ref);
        $__qtd = $PRD->obter_valor(NULL,$a_data_list['produto'][$__k]['cmp']);
        $_i=0; foreach($CT_a_qtd_atacado AS $k=>$v) $_qtd[$k]=$__qtd[$_i++];
        $a_data_list['produto'][$__k]['qtd'] = $_qtd;
      }
    }

    // adiciona
    if(strlen($_ref)>0)
    {
      $_inf = $PRD->obter_prd_por_ref($_ref);
      $_cmp = $PRD->obter_componente();
      $__qtd = $PRD->obter_valor();
      $_i=0; foreach($CT_a_qtd_atacado AS $k=>$v) $_qtd[$k]=$__qtd[$_i++];
      
      $__x = array();
      if(count($_inf)>0) $__x['inf'] = $_inf;
      if(count($_qtd)>0) $__x['qtd'] = $_qtd;
      if(count($_cmp)>0) $__x['cmp'] = $_cmp;
      $_anti_pdup = count($a_data_list['produto']);   # problema que causa duplicação na adição do primeiro registro (as vezes, misteriosamente)
      if(count($_inf)>0)
      {
        $a_data_list['produto'][(gen_tmp_id())]=$__x;
        $a_data_list['produto'] = array_slice($a_data_list['produto'],0,($_anti_pdup + 1));
      }
    }

    $_SESSION[($_POST['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('A_ORCAMENTO_PRODUTOS', $a_data_list['produto']);
    break;

    // -------------------------------------------------------------------------

    case 'criar'   :
    $a_orcamento = $_POST['orcamento'];
    $a_orcamento['id_cliente']=$_POST['__id_cliente'];
    $a_data_list = unserialize($_SESSION[($_POST['tmp_id'])]);
    $_a_prd_cust = array();

    foreach((array)$a_data_list['produto'] AS $k=>$_a)
    {
      foreach((array)$_a['qtd'] AS $_qtd=>$_val)
      {
        $_a_prd_cust[] = array
        (
          'id_produto'  => $_a['inf']['id_produto'],
          'qtd_atacado' => $_qtd,
          'venda_un'    => $_val
        );
      }
    }
    
    $ORC = new Orcamento();
    $_id = $ORC->gravar($a_orcamento,$_a_prd_cust);

    # AJUSTa OS PRODUTOS DO ORCAMENTOS E SEUS RESPECTIVOS CMPONENTES
    $_a_prd_cmp = array();
    $OPC = new _orcamento_prd_cmp();
    
    foreach((array)$OPC->di_all(array('id_orcamento' => $_id)) AS $__k=>$__v)
    {
      $OPC->di_reset();
      $OPC->di_set($__v);
      $OPC->di_del();
    }

    foreach((array)$a_data_list['produto'] AS $k=>$_a)
    {
      foreach((array)$_a['cmp'] AS $_k=>$__a)
      {
        $_a_prd_cmp = array
        (
          'id_orcamento'  => $_id,
          'id_produto'    => $_a['inf']['id_produto'],
          'id_componente' => $__a['produto_componente']['id_componente'],
          'quantidade'    => $__a['produto_componente']['quantidade']
        );
        $OPC->di_reset();
        $OPC->di_set($_a_prd_cmp);
        $OPC->di_add();
      }
    }

    if($a_orcamento['aprovado'] == 'S' && $_id>0)
    {
      $PED = new Pedido();
      $a_ped = array
      (
        'id_orcamento'  => $_id,
        'id_cliente'    => $a_orcamento['id_cliente'],
        'id_usuario'    => $a_orcamento['id_usuario'],
        'id_midia'      => $a_orcamento['id_midia'],
        'id_cond_pagto' => $a_orcamento['id_cond_pagto'],
        'custo_base'    => $a_orcamento['custo_base'],
        'dt_emissao'    => date('d/m/Y'),
        'prazo_entrega' => $a_orcamento['prazo_entrega'],
        'entregue'      => 'N',
        'observacao'    => $a_orcamento['observacao'],
        'gravacao'      => $a_orcamento['gravacao'],
        'cor'           => $a_orcamento['cor']
      );
      $a_prd = array();
      foreach((array)$a_data_list['produto'] AS $_k=>$_a)
      {
        $a_prd[] = array
        (
          'id_produto' => $_a['inf']['id_produto'],
          'ioc'        => $_a['inf']['id'],
          'quantidade' => '0',
          'venda_un'   => '0.000'
        );
      }
      $_id_ped = $PED->gravar($a_ped,$a_prd);

      header("Location: ./index.php?view=venped&action=alterar&id_pedido=".$_id_ped);
      exit;
    }

    header("Location: ".$CNF_s_base_url . "&action=alterar&id_orcamento=".$_id);
    exit;
    
    break;

    // -------------------------------------------------------------------------

    case 'alterar'   :
    $ORC = new Orcamento();
    $a_orcamento = $_POST['orcamento'];
    $a_orcamento['id_cliente']=$_POST['__id_cliente'];
    $a_data_list = unserialize($_SESSION[($_POST['tmp_id'])]);
    $_a_prd_cust = array();

    foreach((array)$a_data_list['produto'] AS $k=>$_a)
    {
      foreach((array)$_a['qtd'] AS $_qtd=>$_val)
      {
        $_a_prd_cust[] = array
        (
          'id_produto'  => $_a['inf']['id_produto'],
          'qtd_atacado' => $_qtd,
          'venda_un'    => $_val
        );
      }
    }

    $_id = $ORC->gravar($a_orcamento,$_a_prd_cust);
    
    # AJUSTa OS PRODUTOS DO ORCAMENTOS E SEUS RESPECTIVOS CMPONENTES
    $_a_prd_cmp = array();
    $OPC = new _orcamento_prd_cmp();

    foreach((array)$OPC->di_all(array('id_orcamento' => $_id)) AS $__k=>$__v)
    {
      $OPC->di_reset();
      $OPC->di_set($__v);
      $OPC->di_del();
    }

    foreach((array)$a_data_list['produto'] AS $k=>$_a)
    {
      foreach((array)$_a['cmp'] AS $_k=>$__a)
      {
        $_a_prd_cmp = array
        (
          'id_orcamento'  => $_id,
          'id_produto'    => $_a['inf']['id_produto'],
          'id_componente' => $__a['produto_componente']['id_componente'],
          'quantidade'    => $__a['produto_componente']['quantidade']
        );
        $OPC->di_reset();
        $OPC->di_set($_a_prd_cmp);
        $OPC->di_add();
      }
    }

    if($a_orcamento['aprovado'] == 'S' && $_id>0)
    {
      $PED = new Pedido();
      $a_ped = array
      (
        'id_orcamento'  => $_id,
        'id_cliente'    => $a_orcamento['id_cliente'],
        'id_usuario'    => $a_orcamento['id_usuario'],
        'id_midia'      => $a_orcamento['id_midia'],
        'id_cond_pagto' => $a_orcamento['id_cond_pagto'],
        'custo_base'    => $a_orcamento['custo_base'],
        'dt_emissao'    => date('d/m/Y'),
        'prazo_entrega' => $a_orcamento['prazo_entrega'],
        'entregue'      => 'N',
        'observacao'    => $a_orcamento['observacao'],
        'gravacao'      => $a_orcamento['gravacao'],
        'cor'           => $a_orcamento['cor']
      );
      $a_prd = array();
      foreach((array)$a_data_list['produto'] AS $_k=>$_a)
      {
        $a_prd[] = array
        (
          'id_produto' => $_a['inf']['id_produto'],
          'ioc'        => $_a['inf']['id'],
          'quantidade' => '0',
          'venda_un'   => '0.000'
        );
      }
      $_id_ped = $PED->gravar($a_ped,$a_prd);

      header("Location: ./index.php?view=venped&action=alterar&id_pedido=".$_id_ped);
      exit;
    }
    
    header("Location: ./index.php?view=venorc&action=alterar&id_orcamento=".$_id);
    exit;
    
    //$safe_view = '_info_';
    //$o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' ALTERADO');
    break;

    // -------------------------------------------------------------------------

    case 'email' :
    $a_mail_data = unserialize($_SESSION[($_POST['tmp_id'])]);
    
    $recipients = $_POST['mail']['to'] . ", copias@confiancebrindes.com.br";
    
    $headers["From"]         = $_POST['mail']['from'];
    $headers["To"]           = $_POST['mail']['to'];
    #$headers["Cc"]           = "copias@confiancebrindes.com.br";
    $headers["Subject"]      = $_POST['mail']['title'];
    $headers["MIME-Version"] = "1.0";
    $headers["Content-Type"] = "text/html; charset=ISO-8859-1";

    $o_smarty->assign($a_mail_data);
    $o_smarty->assign('position', 'absolute');
    $body = $o_smarty->fetch('_email.tpl.htm');

    $params["host"]     = "smtp.confiancebrindes.com.br";
    $params["port"]     = "25";
    $params["auth"]     = true;
    $params["username"] = "vendas@confiancebrindes.com.br";
    $params["password"] = "vd001";

    // Create the mail object using the Mail::factory method
    $mail_object =& Mail::factory("smtp", $params);
    $mail_object->send($recipients, $headers, $body);

    echo "<center><font color=red>Mensagem enviada com sucesso</font></center><br>\n";
    break;

    // -------------------------------------------------------------------------
  }
}

// =============================================================================
// PEDIDOS
// =============================================================================

// GET ACTIONS
//
elseif($safe_view == 'venped' && $_SERVER['REQUEST_METHOD']=='GET')
{
  if($safe_action == 'criar') $safe_action = 'listar';
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'clientes' :
    if($_GET['cliente'])
    {
      $CLI = new Cliente();
      $o_smarty->assign('A_CLIENTES',$CLI->buscar($_GET['cliente'],'fantasia','razao_social'));
    }
    break;

    // -------------------------------------------------------------------------

    case 'produtos' :     // a parte de adicionar / alterar está no post  por limitacoes do get
    if($_GET['tmp_id']) $a_data_list=unserialize($_SESSION[($_GET['tmp_id'])]);

    // remove
    if($_GET['keydel']!==NULL)
    {
      unset($a_data_list['produto'][($_GET['keydel'])]);
    }

    $_SESSION[($_GET['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('ENTREGUE', $a_data_list['form']['entregue']);
    $o_smarty->assign('FORBID', $a_data_list['form']['forbid']);
    $o_smarty->assign('A_PEDIDO_PRODUTOS', $a_data_list['produto']);
    break;

    // -------------------------------------------------------------------------

    case 'listar' :
    $PED = new Pedido();
    $safe_sort = $safe_sort ? $safe_sort : 'id_pedido desc';
    $a_ped = $PED->listar($CNF_s_page_size,($safe_page*$CNF_s_page_size),$safe_sort);
    $i_ped_total = $PED->total();
    # GAMBIARRA P PEGAR FANTASIA
    foreach((array)$a_ped AS $k=>$v)
    {
      $_CLI = new Cliente($v['id_cliente']);
      $_a_cli = $_CLI->obter_cliente();
      $a_ped[$k]['_cliente_fantasia'] = $_a_cli['fantasia'];
    }
    # GAMBIARRA P FORMATAR DATA
    foreach($a_ped AS $_apedk=>$apedv)
    {
      $a_ped[$_apedk]['dt_emissao'] = date2data($a_ped[$_apedk]['dt_emissao']);
      $a_ped[$_apedk]['dt_entrega'] = date2data($a_ped[$_apedk]['dt_entrega']);
    }

    $o_smarty->assign('PAGES', range(1,ceil($i_ped_total/$CNF_s_page_size)));
    $o_smarty->assign('A_PEDIDOS', $a_ped);
    $o_smarty->assign('PEDIDOS_TOTAL', $i_ped_total);
    break;

    // -------------------------------------------------------------------------

    case 'pesquisar' :
    break;

    // -------------------------------------------------------------------------

    case 'buscar'  :
    $PED = new Pedido();
    $_a_busca = $_GET['pedido'];
    if($_GET['__id_cliente']) $_a_busca['id_cliente'] = $_GET['__id_cliente'];

    // BUSCA ENTRE DATAS ...
    //
    if($_a_busca['dt_emissao'] && $_a_busca['dt_emissao_ate'])
    {
      $s_qry  = "SELECT * FROM pedido ";
      $s_qry .= "WHERE dt_emissao BETWEEN \"". data2date($_a_busca['dt_emissao']) ."\" AND \"" . data2date($_a_busca['dt_emissao_ate']) . "\" ";
      if($_a_busca['id_cliente']) $s_qry .= " AND id_cliente = \"" .$_a_busca['id_cliente'] . "\"";
      if($_a_busca['id_usuario']) $s_qry .= " AND id_usuario = \"" .$_a_busca['id_usuario'] . "\"";
      if($_a_busca['entregue'])   $s_qry .= " AND entregue   = \"" .$_a_busca['entregue']   . "\"";

      $a_ped = $PED->o_pedido->di_exec($s_qry);
    }
    else
    {
      $a_ped = $PED->buscar($_a_busca,$safe_sort,$s_extra);
    }


    $i_ped_total = count($a_ped);

    if($i_ped_total==1)
    {
      $safe_action = 'alterar';

      // ----------- AQUI E IGUAL O ALTERAR ----

      $a_data_list = array();
      $PED = new Pedido($a_ped[0]['id_pedido']);
      $a_data_list['form']=$PED->obter_pedido();

      $_last_id_prd = 0;
      foreach((array)$PED->obter_pcs() AS $_a)
      {
        if($_last_id_prd != $_a['id_produto'])
        {
          $_PRD = new Produto($_a['id_produto']);
          $_a_prd = $_PRD->obter_produto();
          $prd_tmp_id = gen_tmp_id();
          $a_data_list['produto'][$prd_tmp_id]['inf'] = $_a;
          $a_data_list['produto'][$prd_tmp_id]['inf']['referencia'] = $_a_prd['referencia'];
          $a_data_list['produto'][$prd_tmp_id]['inf']['descricao'] = $_a_prd['descricao'];
        }
        $a_data_list['produto'][$prd_tmp_id]['quantidade']=$_a['quantidade'];
        $a_data_list['produto'][$prd_tmp_id]['venda_un']=$_a['venda_un'];
        $a_data_list['produto'][$prd_tmp_id]['total']=number_format($_a['venda_un'],2) * $_a['quantidade'];
      }
      $s_tmp_id = gen_tmp_id();

      $_CLI = new Cliente($a_data_list['form']['id_cliente']);
      $o_smarty->assign('A_CLIENTE', $_CLI->obter_cliente());
      $_SESSION[$s_tmp_id]=serialize($a_data_list);
      $MID = new Midia();
      $o_smarty->assign('A_MIDIAS', $MID->listar_tudo());
      $CDP = new CondPagto();
      $o_smarty->assign('A_COND_PAGTO', $CDP->listar_tudo());
      $o_smarty->assign('A_PEDIDO', $a_data_list['form']);
      $o_smarty->assign('TMP_ID', $s_tmp_id);

      // ----------- AQUI E IGUAL O ALTERAR ----
    }
    else
    {
      # GAMBIARRA P PEGAR FANTASIA
      foreach((array)$a_ped AS $k=>$v)
      {
        $_CLI = new Cliente($v['id_cliente']);
        $_a_cli = $_CLI->obter_cliente();
        $a_ped[$k]['_cliente_fantasia'] = $_a_cli['fantasia'];
      }
      # GAMBIARRA P FORMATAR DATA
      foreach($a_ped AS $_apedk=>$apedv)
      {
        $a_ped[$_apedk]['dt_emissao'] = date2data($a_ped[$_apedk]['dt_emissao']);
        $a_ped[$_apedk]['dt_entrega'] = date2data($a_ped[$_apedk]['dt_entrega']);
      }

      $a_data_list = array ('pedidos'=>$a_ped);
      $s_tmp_id = gen_tmp_id();
      $_SESSION[$s_tmp_id]=serialize($a_data_list);
      $o_smarty->assign('TMP_ID', $s_tmp_id);

      $o_smarty->assign('PAGES', range(1,ceil($i_ped_total/$CNF_s_page_size)));
      $o_smarty->assign('A_PEDIDOS', array_slice($a_ped,($safe_page*$CNF_s_page_size),$CNF_s_page_size));
      $o_smarty->assign('PEDIDOS_TOTAL', $i_ped_total);
    }
    break;

    // -------------------------------------------------------------------------

    case 'alterar' :
    if($_GET['id_pedido']) $PED = new Pedido($_GET['id_pedido']);
    elseif($_GET['pedido']['id_pedido']) $PED = new Pedido($_GET['pedido']['id_pedido']);

    $_a_data_list = unserialize($_SESSION[($_GET['tmp_id'])]);
    if(is_array($_a_data_list))
    {
      $a_data_list = $_a_data_list;
      $s_tmp_id = $_GET['tmp_id'];
    }
    else
    {
      $_last_prd_id = 0;
      foreach((array)@$PED->obter_pcs() AS $_a)
      {
        if($_last_id_prd != $_a['id_produto'])
        {
          $_PRD = new Produto($_a['id_produto']);
          $_a_prd = $_PRD->obter_produto();
          $prd_tmp_id = gen_tmp_id();
          $a_data_list['produto'][$prd_tmp_id]['inf'] = $_a;
          $a_data_list['produto'][$prd_tmp_id]['inf']['referencia'] = $_a_prd['referencia'];
          $a_data_list['produto'][$prd_tmp_id]['inf']['descricao'] = $_a_prd['descricao'];
        }
        $a_data_list['produto'][$prd_tmp_id]['quantidade']=$_a['quantidade'];
        $a_data_list['produto'][$prd_tmp_id]['venda_un']=$_a['venda_un'];
        $a_data_list['produto'][$prd_tmp_id]['total']=number_format($_a['venda_un'],2) * $_a['quantidade'];
      }
      $s_tmp_id = gen_tmp_id();
    }

    if($_GET['pedido'])
    {
      $a_data_list['form']=$_GET['pedido'];
      $a_data_list['form']['id_cliente']=$_GET['__id_cliente'];
    }
    else
    {
      $a_data_list['form']=$PED->obter_pedido();
    }

#echo "<pre>";
#print_r($a_data_list['produto']);
#echo "</pre>";

    $_CLI = new Cliente($a_data_list['form']['id_cliente']);
    $o_smarty->assign('A_CLIENTE', $_CLI->obter_cliente());
    $_SESSION[$s_tmp_id]=serialize($a_data_list);
    $MID = new Midia();
    $o_smarty->assign('A_MIDIAS', $MID->listar_tudo());
    $CDP = new CondPagto();
    $o_smarty->assign('A_COND_PAGTO', $CDP->listar_tudo());
    $o_smarty->assign('A_PEDIDO', $a_data_list['form']);
    $o_smarty->assign('TMP_ID', $s_tmp_id);

    break;

    // -------------------------------------------------------------------------

    case 'remover' :
    $PED = new Pedido($_GET['id_pedido']);
    $_a_ped = $PED->obter_pedido();
    
    $_id = $PED->remover();
    
    // CASO HOUVER UM ORÇAMENTO VINCULADO, ABRE ESTE ORCAMENTO
    //
    if($_a_ped['id_orcamento'] > 0)
    {
      $ORC = new Orcamento($_a_ped['id_orcamento']);
      $_a_orc = array('aprovado'=>'N');
      $ORC->o_orcamento->di_set($_a_orc);
      $ORC->o_orcamento->di_upd();
      header("Location: ". $CNF_s_base_url ."&view=venorc&action=alterar&id_orcamento=". $_a_ped['id_orcamento']);
      exit;
    }
    else
    {
      $safe_view = '_info_';
      $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' REMOVIDO');
    }
    break;

    // -------------------------------------------------------------------------

    case 'email' :
    $a_data_list = unserialize($_SESSION[($_GET['tmp_id'])]);
    $_CLI = new Cliente($a_data_list['form']['id_cliente']);
    $_a_cli = $_CLI->obter_cliente();
    $_CPG = new CondPagto($a_data_list['form']['id_cond_pagto']);
    $_a_cpg = $_CPG->obter_cond_pagto();
    $_ORC = new Orcamento($a_data_list['form']['id_orcamento']);
    $_a_orc = $_ORC->obter_orcamento();

    $a_mail_data = array();

    $a_mail_data['DIA'] = date('d');
    $a_mail_data['MES'] = $CT_a_month_list[date('n')];
    $a_mail_data['ANO'] = date('Y');
    $a_mail_data['ID'] = $a_data_list['form']['id_pedido'];
    $a_mail_data['OBSERVACAO'] = $a_data_list['form']['observacao'];
    $a_mail_data['FANTASIA'] = $_a_cli['fantasia'];
    $a_mail_data['CNPJ'] = $_a_cli['cnpj_cpf'];
    $a_mail_data['IE'] = $_a_cli['ie_rg'];
    $a_mail_data['CEP'] = $_a_cli['cep'];
    $a_mail_data['RAZAO_SOCIAL'] = $_a_cli['razao_social'];
    if($_a_cli['endereco']) $a_mail_data['ENDERECO'] = $_a_cli['endereco'];
    if($_a_cli['bairro'])   $a_mail_data['ENDERECO'].= " - ". $_a_cli['bairro'];
    if($_a_cli['cidade'])   $a_mail_data['ENDERECO'].= " - ". $_a_cli['cidade'];
    if($_a_cli['uf'])       $a_mail_data['ENDERECO'].= " - ". $_a_cli['uf'];;
    $a_mail_data['CONTATO'] = $_a_cli['contato'];
    $a_mail_data['TEL'] = $_a_cli['telefone_1'];
    $a_mail_data['FAX'] = $_a_cli['telefone_fax'];
    $a_mail_data['EMAIL'] = $_a_cli['email'];

    foreach((array)$a_data_list['produto'] AS $_id=>$_ap)
    {
      $a_mail_data['TABELA'][$_id]['referencia'] = $_ap['inf']['referencia'];
      $a_mail_data['TABELA'][$_id]['descricao'] = $_ap['inf']['descricao'];
      $a_mail_data['TABELA'][$_id]['quantidade'] = $_ap['quantidade'];
      $a_mail_data['TABELA'][$_id]['venda_un'] = $_ap['venda_un'];
      $a_mail_data['TABELA'][$_id]['total'] = $_ap['total'];
      $a_mail_data['TOTAL']['quantidade'] += $_ap['quantidade'];
      $a_mail_data['TOTAL']['total'] += $_ap['total'];
    }

    $a_mail_data['COND_PGTO'] = $_a_cpg['nome'];
    $a_mail_data['PRAZO_ENTREGA'] = $a_data_list['form']['prazo_entrega'];
    $a_mail_data['FOTOLITO'] = $a_data_list['form']['custo_base'];
    $a_mail_data['GRAVACAO'] = $a_data_list['form']['gravacao'];
    $a_mail_data['COR'] = $a_data_list['form']['cor'];
    $a_mail_data['REPRESENTANTE'] = $CT_a_tmp_usuarios[($_a_orc['id_usuario'])];
    $a_mail_data['VENDEDOR'] = $CT_a_tmp_usuarios[($a_data_list['form']['id_usuario'])];
    $a_mail_data['EMAIL_VENDEDOR'] = 'vendas@confiancebrindes.com.br';

    $s_tmp_id = gen_tmp_id();
    $_SESSION[$s_tmp_id] = serialize($a_mail_data);
    $o_smarty->assign('TMP_ID', $s_tmp_id);
    $o_smarty->assign('MAIL_DATA', $a_mail_data);

    break;

    // -------------------------------------------------------------------------

    case 'tab' :
    $a_data_list = unserialize($_SESSION[($_GET['tmp_id'])]);
    $a_pedidos = $a_data_list['pedidos'];
    
    
    
    
    $output  = "PARA IMPRIMIR, CLIQUE COM O BOTÃO DIREITO E DEPOIS 'Imprimir' ou 'Print'<br><br>\n";
    $output .= "<table border=0 cellpadding=0 cellspacing=1 width=650><tr>";
    $output .= "<td class=textoform><b>Nº</b></td>";
    $output .= "<td class=textoform><b>CLIENTE</b></td>";
    $output .= "<td class=textoform><b>VENDEDOR</b></td>";
    $output .= "<td class=textoform><b>DT. EMISSAO</b></td>";
    $output .= "<td class=textoform><b>PRAZO</b></td>";
    $output .= "<td class=textoform><b>ENTREGUE</b></td>";
    $output .= "<td class=textoform><b>VALOR</b></td></tr>\n";

    for($i=0;$i<count($a_pedidos);$i++)
    {
        $output .= "<tr>";
        $output .= "<td class=mintextoform nowrap><b>". $a_pedidos[$i]['id_pedido'] . "</b>&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform>". $a_pedidos[$i]['_cliente_fantasia'] . "&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform align=right>". $CT_a_tmp_usuarios[($a_pedidos[$i]['id_usuario'])] . "&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform align=right>". $a_pedidos[$i]['dt_emissao'] . "&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform align=right>". $a_pedidos[$i]['prazo_entrega'] . "&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform align=center>". $a_pedidos[$i]['entregue'] . "&nbsp;&nbsp;</td>";
        $output .= "<td class=mintextoform align=right>". number_format($a_pedidos[$i]['valor'],2,',','') . "&nbsp;&nbsp;</td>";
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    $o_smarty->assign('PEDTABLE', $output);

    #for($i=0;$i<count($a_pedidos);$i++)
    #{
    #
    #}
    break;

    // -------------------------------------------------------------------------

    case 'viseml' :
    echo "<input type=button value=imprimir OnClick='window.print()'><br>\n";
    $a_mail_data = unserialize($_SESSION[($_GET['tmp_id'])]);
    #echo "<pre>";
    #print_r($a_mail_data);
    #echo "</pre>";
    $o_smarty->assign($a_mail_data);
    $o_smarty->display('_email2.tpl.htm'); exit;
    break;

    // -------------------------------------------------------------------------
  }
}
// POST ACTIONS
//
elseif($safe_view == 'venped' && $_SERVER['REQUEST_METHOD']=='POST')
{
  switch($safe_action)
  {
    // -------------------------------------------------------------------------

    case 'produtos' : // a parte de del esta no get
    if($_POST['tmp_id']) $a_data_list=unserialize($_SESSION[($_POST['tmp_id'])]);

    // atualiza quantidades ..
    foreach((array)$_POST['produto'] AS $k=>$_a_v)
    $a_data_list['produto'][$k]['quantidade'] = $_a_v['quantidade'];

    // recalcula valores ...
    $_a_opv_list = array();
    $ORC = new Orcamento($a_data_list['form']['id_orcamento']);
    $_last_qtdata = 0;
    $_i_mtasd = gen_tmp_id();
    foreach((array)@$ORC->obter_opv() AS $k=>$_a_opv)
    {
      if($_a_opv['qtd_atacado']<$_last_qtdata) $_i_mtasd = gen_tmp_id();
      $_a_opv_list[$_i_mtasd][($_a_opv['qtd_atacado'])] = $_a_opv['venda_un'];
      $_last_qtdata = $_a_opv['qtd_atacado'];
    }
    foreach($a_data_list['produto'] AS $_k=>$_a_v)
    {
      $its_data_orc = $_a_opv_list[key($_a_opv_list)]; next($_a_opv_list);
      $a_data_list['produto'][$_k]['venda_un'] = $its_data_orc[in_qtd_range($_a_v['quantidade'])];
      $a_data_list['produto'][$_k]['total'] = number_format($a_data_list['produto'][$_k]['venda_un'],2) * $_a_v['quantidade'];
    }

    $_SESSION[($_POST['tmp_id'])]=serialize($a_data_list);
    $o_smarty->assign('A_PEDIDO_PRODUTOS', $a_data_list['produto']);
    break;

    // -------------------------------------------------------------------------

    case 'alterar'   :
    $PED = new Pedido();
    $a_pedido = $_POST['pedido'];
    $a_pedido['id_cliente']=$_POST['__id_cliente'];
    $a_data_list = unserialize($_SESSION[($_POST['tmp_id'])]);
    $_a_produtos = array();
    foreach((array)$a_data_list['produto'] AS $_k=>$_a_v)
    {
      $_a_produtos[] = array
      (
        'id_pedido'  => $_a_v['inf']['id_pedido'],
        'id_produto' => $_a_v['inf']['id_produto'],
        'quantidade' => $_a_v['quantidade'],
        'venda_un'   => $_a_v['venda_un']
      );
      $__total = $_a_v['quantidade'] * $_a_v['venda_un'];
    }
    $a_pedido['valor'] = $__total;
    
    if($a_pedido['entregue'] == 'S')
    {
      if($_POST['_dt_entrega']) $a_pedido['dt_entrega'] = $_POST['_dt_entrega'];
      else $a_pedido['dt_entrega'] = date("d/m/Y");
    }
    
    $_id = $PED->gravar($a_pedido,$_a_produtos);
    
    header("Location: ./index.php?view=venped&action=alterar&id_pedido=".$_id);
    exit;

    $safe_view = '_info_';
    $o_smarty->assign('INFO', 'REGISTRO CÓDIGO '.($_id).' ALTERADO');
    break;

    // -------------------------------------------------------------------------

    case 'email' :
    $a_mail_data = unserialize($_SESSION[($_POST['tmp_id'])]);

    $recipients = $_POST['mail']['to'];

    $headers["From"]         = $_POST['mail']['from'];
    $headers["To"]           = $_POST['mail']['to'];
    $headers["Subject"]      = $_POST['mail']['title'];
    $headers["MIME-Version"] = "1.0";
    $headers["Content-Type"] = "text/html; charset=ISO-8859-1";

    $o_smarty->assign($a_mail_data);
    $o_smarty->assign('position', 'absolute');
    $body = $o_smarty->fetch('_email2.tpl.htm');

    $params["host"]     = "smtp.confiancebrindes.com.br";
    $params["port"]     = "25";
    $params["auth"]     = true;
    $params["username"] = "vendas@confiancebrindes.com.br";
    $params["password"] = "vd001";

    // Create the mail object using the Mail::factory method
    $mail_object =& Mail::factory("smtp", $params);
    $mail_object->send($recipients, $headers, $body);

    echo "<center><font color=red>Mensagem enviada com sucesso</font></center><br>\n";
    break;

    // -------------------------------------------------------------------------
  }
}

// =============================================================================
// HISTÓRICO DE VENDAS
// =============================================================================

elseif($safe_view == 'venlog')
{
}


/* VIEW CHECK */
if($o_smarty->template_exists($safe_view . '.tpl.htm'))
{
  $o_smarty->assign('VIEW'        , $safe_view);
  $o_smarty->assign('VIEW_FILE'   , $safe_view . '.tpl.htm');
  $o_smarty->assign('ACTION'      , $safe_action);
  $o_smarty->assign('PAGE'        , $safe_page);
  $o_smarty->assign('SORT'        , $safe_sort);
  $o_smarty->assign('SHOW_MENU'   , $safe_show_menu);
  $o_smarty->display('_molde.tpl.htm'); exit;
}
else
{
  $o_smarty->assign('ERROR', 'TEMPLATE FILE '.$safe_view.'.tpl.htm NOT FOUND!');
  $o_smarty->display('_erro_.tpl.htm'); exit;
}

?>
