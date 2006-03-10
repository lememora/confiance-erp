<?php

/**
 * <b><u>Sistema de Gerenciamento Comercial</u></b>
 * <p>Classes de módulos SGC (SGC_modcl.inc.php)</p>
 * @author Rafael Moraes
 */

// {{{ CLASSES DE MÓDULOS .

/* -----------------------------------------------------------------------------
 * Componente .
 * ---------------------------------------------------------------------------*/
 
class Componente
{
  var $o_componente;
  var $o_tabela;

  function Componente($id_cmp=NULL)
  {
    $this->o_componente = new _produto_componente($id_cmp);
    $this->o_tabela = new _componente_tabela();
  }

  function gravar($a_cmp=array(),$a_tab=array())
  {
    $this->o_componente->di_set($a_cmp);
    
    if($a_cmp[$this->o_componente->_field_id]==NULL)
    $this->o_componente->di_add();
    else
    $this->o_componente->di_upd();

    foreach($a_tab AS $row)
    {
      if(count($row)>0)
      {
        $row[$this->o_componente->_field_id]=$this->o_componente->{$this->o_componente->_field_id};
        $this->o_tabela->di_reset();
        $this->o_tabela->di_set($row);

        if($row[$this->o_tabela->_field_id]==NULL)
        $this->o_tabela->di_add();
        else
        $this->o_tabela->di_upd();
      }
    }
    return $this->o_componente->{$this->o_componente->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_componente->di_list($nrows,$offset,$sort);
  }
  
  function listar_tudo()
  {
    return $this->o_componente->di_all();
  }
  
  function total()
  {
    return $this->o_componente->di_count();
  }
  
  function buscar($a_cmp=array(),$sort=NULL)
  {
    $this->o_componente->di_set($a_cmp);
    return $this->o_componente->di_search($sort);
  }
  
  function obter_componente()
  {
    return $this->o_componente->di_get();
  }

  function obter_tabela()
  {
    return $this->o_tabela->di_all(array($this->o_componente->_field_id=>$this->o_componente->{$this->o_componente->_field_id}));
  }

  function remover()
  {
    $this->o_componente->di_del();

    foreach((array)$this->obter_tabela() AS $row)
    {
      $this->o_tabela->di_reset();
      $this->o_tabela->di_set($row);
      $this->o_tabela->di_del();
    }
    return $this->o_componente->{$this->o_componente->_field_id};
  }
}

/* -----------------------------------------------------------------------------
 * Custo .
 * ---------------------------------------------------------------------------*/
 
class Custo
{
  var $o_custo;

  function Custo($id_cst=NULL)
  {
    $this->o_custo = new _custo($id_cst);
  }

  function gravar($a_cst=array())
  {
    $this->o_custo->di_set($a_cst);
    
    if($a_cst[$this->o_custo->_field_id]==NULL)
    $this->o_custo->di_add();
    else
    $this->o_custo->di_upd();

    return $this->o_custo->{$this->o_custo->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_custo->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_custo->di_all();
  }

  function total()
  {
    return $this->o_custo->di_count();
  }

  function buscar($a_cst=array(),$sort=NULL)
  {
    $this->o_custo->di_set($a_cst);
    return $this->o_custo->di_search($sort);
  }

  function obter_custo()
  {
    return $this->o_custo->di_get();
  }

  function remover()
  {
    $this->o_custo->di_del();
    return $this->o_custo->{$this->o_custo->_field_id};
  }
}

/* -----------------------------------------------------------------------------
 * Produto .
 * ---------------------------------------------------------------------------*/
 
class Produto
{
  var $o_produto;
  var $o_produto_componente;
  var $o_produto_custo;

  function Produto($id_prd=NULL)
  {
    $this->o_produto = new _produto($id_prd);
    $this->o_produto_cmp = new _produto_componente();
    $this->o_produto_tab = new _componente_tabela();
    $this->o_produto_cst = new _produto_custo();
    $this->o_produto_ppv = new _produto_percentual_venda();
  }

  function gravar($a_prd=array(),$a_cmp=array(),$a_cst=array(),$a_ppv=array())
  {
    // GRAVA PRODUTO.
    $this->o_produto->di_set($a_prd);

    if($a_prd[$this->o_produto->_field_id]==NULL)
    $this->o_produto->di_add();
    else
    $this->o_produto->di_upd();

    // GRAVA COMPONENTES, CUSTOS E PPV.
    
    // dELETION LIST .
    $_tmp_cmp = new _produto_componente();
    $_tmp_tab = new _componente_tabela();
    foreach((array)$this->obter_componente() AS $_row)
    {
      $_tmp_cmp->di_reset();
      $_tmp_cmp->di_set($_row['produto_componente']);
      $_tmp_cmp->di_del();
      foreach($_row['componente_tabela'] AS $__row)
      {
        $_tmp_tab->di_reset();
        $_tmp_tab->di_set($__row);
        $_tmp_tab->di_del();
      }
    }

    foreach((array)$a_cmp AS $row)
    {
      if(count($row)>0)
      {
        $_a_cmp = $row['produto_componente'];
        $_a_tab = $row['componente_tabela'];

        $_a_cmp[$this->o_produto->_field_id]=$this->o_produto->{$this->o_produto->_field_id};
        $this->o_produto_cmp->di_reset();
        $this->o_produto_cmp->di_set($_a_cmp);

        #if($_a_cmp[$this->o_produto_cmp->_field_id]==NULL)
        $this->o_produto_cmp->di_add();
        #else
        #$this->o_produto_cmp->di_upd();

        $_cmp_id = $this->o_produto_cmp->id_componente;

        foreach($_a_tab AS $k=>$__a_tab)
        {
          $__a_tab['id_componente'] = $_cmp_id;
          $this->o_produto_tab->di_reset();
          $this->o_produto_tab->di_set($__a_tab);
          $this->o_produto_tab->di_add();
        }
      }
    }
    foreach((array)$a_cst AS $row)
    {
      if(count($row)>0)
      {
        $row[$this->o_produto->_field_id]=$this->o_produto->{$this->o_produto->_field_id};
        $this->o_produto_cst->di_reset();
        $this->o_produto_cst->di_set($row);
        
        if($row[$this->o_produto_cst->_field_id]==NULL)
        $this->o_produto_cst->di_add();
        else
        $this->o_produto_cst->di_upd();
      }
    }
    foreach((array)$a_ppv AS $row)
    {
      if(count($row)>0)
      {
        $row[$this->o_produto->_field_id]=$this->o_produto->{$this->o_produto->_field_id};
        $this->o_produto_ppv->di_reset();
        $this->o_produto_ppv->di_set($row);

        if($row[$this->o_produto_ppv->_field_id]==NULL)
        $this->o_produto_ppv->di_add();
        else
        $this->o_produto_ppv->di_upd();
      }
    }

    return $this->o_produto->{$this->o_produto->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_produto->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_produto->di_all();
  }

  function total()
  {
    return $this->o_produto->di_count();
  }

  function buscar($a_prd=array(),$sort=NULL)
  {
    $this->o_produto->di_set($a_prd);
    return $this->o_produto->di_search($sort);
  }

  function obter_produto()
  {
    return $this->o_produto->di_get();
  }
  
  function obter_prd_por_ref($ref)
  {
    $_a = $this->o_produto->di_all(array('referencia'=>$ref));
    if(count($_a)==1) $this->o_produto->di_set($_a[0]);
    return $this->obter_produto();
  }

  function obter_componente()
  {
    $_x = array();
    $_a_cmp = $this->o_produto_cmp->di_all(array($this->o_produto->_field_id=>$this->o_produto->{$this->o_produto->_field_id}));
    foreach((array)$_a_cmp AS $k=>$_a_v)
    {
      $_x[$k]['produto_componente'] = $_a_cmp[$k];
      $_x[$k]['componente_tabela']  = $this->o_produto_tab->di_all(array('id_componente'=>$_a_v['id_componente']));
    }
    return $_x;
  }
  
  function obter_custo()
  {
    return $this->o_produto_cst->di_all(array($this->o_produto->_field_id=>$this->o_produto->{$this->o_produto->_field_id}));
  }

  function obter_ppv()
  {
    return $this->o_produto_ppv->di_all(array($this->o_produto->_field_id=>$this->o_produto->{$this->o_produto->_field_id}));
  }

  function remover()
  {
    $this->o_produto->di_del();

    foreach((array)$this->obter_componente() AS $row)
    {
      $this->o_produto_cmp->di_reset();
      $this->o_produto_cmp->di_set($row);
      $this->o_produto_cmp->di_del();
    }
    foreach((array)$this->obter_custo() AS $row)
    {
      $this->o_produto_cst->di_reset();
      $this->o_produto_cst->di_set($row);
      $this->o_produto_cst->di_del();
    }
    foreach((array)$this->obter_ppv() AS $row)
    {
      $this->o_produto_ppv->di_reset();
      $this->o_produto_ppv->di_set($row);
      $this->o_produto_ppv->di_del();
    }
    return $this->o_produto->{$this->o_produto->_field_id};
  }

  function obter_valor($a_prd=array(),$a_cmp=array(),$a_cst=array(),$a_ppv=array()) // recalc produto p orcamento ...
  {
    if(count($a_prd)==0) $a_prd = $this->obter_produto();
    if(count($a_cmp)==0) $a_cmp = $this->obter_componente();
    if(count($a_cst)==0) $a_cst = $this->obter_custo();
    if(count($a_ppv)==0) $a_ppv = $this->obter_ppv();
    return   $this->calc_ppv($a_prd,$a_cmp,$a_cst,$a_ppv);
  }
  
  function calc_ppv($a_prd,$a_cmp,$a_cst,$a_ppv)
  {
    $a_results = array();
    $pcb = $a_prd['custo_base'];

    // calculo para cada registro do PPV.
    foreach((array)$a_ppv AS $k=>$_a_ppv)
    {
      $qta = $_a_ppv['qtd_atacado'];
      $pct = $_a_ppv['percentual_venda'];
      $C1  = 0;
      $C2  = 0;

      // CALCULO para cada COMPONENTE.
      foreach((array)$a_cmp AS $_a_cmp)
      {
        $_a__cmp = $_a_cmp['produto_componente'];
        $_a__tab = $_a_cmp['componente_tabela'];
        $_id  = $_a__cmp['id_componente'];
        $_qtd = $_a__cmp['quantidade'];
        $_C1  = 0;

        // (CALCULO 1) calculo do valor da tabela de um COMPONENTE.
        // OBTEM APENAS 1 VALOR CORRESPONDENTE.
        foreach((array)$_a__tab AS $__a_tab)
        {
          $__de  = $__a_tab['quantidade_de'];
          $__ate = $__a_tab['quantidade_ate'];
          $__cun = $__a_tab['custo_un'];
          $__max = $__a_tab['valor_max'];

          if (
          (  ($__de>=1 && $__ate==0) &&                   $__max=='S' ) ||
          (  ($__de>=1 && $__ate==0) && ($qta>=$__de ) && $__max=='N' ) ||
          (  ($__de>=1 && $__ate>=1) && ($qta>=$__de && $qta<=$__ate) ) ||
          (  ($__de==0 && $__ate>=1) && ($qta<=$__ate)                )
          )
          {
            if($__ate==0 && $__max=='S')
            {
              #echo "_C1 = __cun * ceil(qta/__de) * __de * _qtd = " . $_C1 ."=". $__cun ."* ceil(".$qta."/".$__de.") * ".$__de ."*". $_qtd. "<br>\n";
              $_C1 = $__cun * ceil($qta/$__de) * $__de * $_qtd;
            }
            elseif($__ate>0 && $__max=='S')
            {
              #echo "_C1 = __cun * __ate * _qtd = ".$_C1 . " = " . $__cun . " * " . $__ate . " * " . $_qtd . "<br>\n";
              $_C1 = $__cun * $__ate * $_qtd;
            }
            else
            {
              $_C1 = $__cun * $qta * $_qtd;
            }
          }
        }
        // valor unitário do CALCULO 2.
        #echo "C1 *= _qtd = " . $_C1 . " *= " .$_qtd. "<br>\n";
        ####$_C1 *= $_qtd;
        #echo "C1 /= qta = " . $_C1 . " /= " .$qta. "<br>\n";
        $_C1 /= $qta;
        $C1  += $_C1;
      }

      // (CALCULO 2) calculo do CUSTO.
      foreach((array)$a_cst AS $_a_cst)
      {
        $_cus = $_a_cst['custo'];
        $_qtd = $_a_cst['quantidade'];
        $_qti = $_a_cst['qtd_intervalo'];
        $C2 += ((int)$_qti>0) ? ($_cus*$_qtd*ceil($qta/$_qti)) : ($_cus*$_qtd);
      }

      // valor unitário do CALCULO 3.
      $C2 /= $qta;

      // CALCULO final.
      //$CF = ($pct/100) * ($pcb+$C1+$C2+$C3);
      $CF = ($pct/100*$pcb) + ($C1+$C2);
      $a_results[$k] = $CF;
    }

    return $a_results;
  }
  
}

/* -----------------------------------------------------------------------------
 * Cliente.
 * ---------------------------------------------------------------------------*/
 
class Cliente
{
  var $o_cliente;

  function Cliente($id_cli=NULL)
  {
    $this->o_cliente = new _cliente($id_cli);
  }

  function gravar($a_cli=array())
  {
    $this->o_cliente->di_set($a_cli);
    
    if($a_cli[$this->o_cliente->_field_id]==NULL)
    $this->o_cliente->di_add();
    else
    $this->o_cliente->di_upd();

    return $this->o_cliente->{$this->o_cliente->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_cliente->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_cliente->di_all();
  }

  function total()
  {
    return $this->o_cliente->di_count();
  }

  function buscar($a_cli=array(),$sort=NULL)
  {
    $this->o_cliente->di_set($a_cli);
    return $this->o_cliente->di_search($sort);
  }

  function obter_cliente()
  {
    return $this->o_cliente->di_get();
  }

  function remover()
  {
    $this->o_cliente->di_del();
    return $this->o_cliente->{$this->o_cliente->_field_id};
  }
}

/* -----------------------------------------------------------------------------
 * Mídia.
 * ---------------------------------------------------------------------------*/
 
class Midia
{
  var $o_midia;

  function Midia($id_mid=NULL)
  {
    $this->o_midia = new _midia($id_mid);
  }

  function gravar($a_mid=array())
  {
    $this->o_midia->di_set($a_mid);

    if($a_mid[$this->o_midia->_field_id]==NULL)
    $this->o_midia->di_add();
    else
    $this->o_midia->di_upd();

    return $this->o_midia->{$this->o_midia->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_midia->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_midia->di_all();
  }

  function total()
  {
    return $this->o_midia->di_count();
  }

  function buscar($a_mid=array(),$sort=NULL)
  {
    $this->o_midia->di_set($a_mid);
    return $this->o_midia->di_search($sort);
  }

  function obter_midia()
  {
    return $this->o_midia->di_get();
  }

  function remover()
  {
    $this->o_midia->di_del();
    return $this->o_midia->{$this->o_midia->_field_id};
  }
}

/* -----------------------------------------------------------------------------
 * Condição de pagamento.
 * ---------------------------------------------------------------------------*/
 
class CondPagto
{
  var $o_cond_pagto;

  function CondPagto($id_cnp=NULL)
  {
    $this->o_cond_pagto = new _cond_pagto($id_cnp);
    $this->o_cpp = new _cond_pagto_parcela();
  }

  function gravar($a_cnp=array(),$a_prc=array())
  {
    $this->o_cond_pagto->di_set($a_prc);

    if($a_prc[$this->o_midia->_field_id]==NULL)
    $this->o_cond_pagto->di_add();
    else
    $this->o_cond_pagto->di_upd();
    
    foreach((array)$a_prc AS $row)
    {
      if(count($row)>0)
      {
        $row[$this->o_->_field_id]=$this->o_cond_pagto->{$this->o_cond_pagto->_field_id};
        $this->o_cpp->di_reset();
        $this->o_cpp->di_set($row);

        if($row[$this->o_cpp->_field_id]==NULL)
        $this->o_cpp->di_add();
        else
        $this->o_cpp->di_upd();
      }
    }

    return $this->o_cond_pagto->{$this->o_cond_pagto->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_cond_pagto->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_cond_pagto->di_all();
  }

  function total()
  {
    return $this->o_cond_pagto->di_count();
  }

  function buscar($a_prc=array(),$sort=NULL)
  {
    $this->o_cond_pagto->di_set($a_prc);
    return $this->o_cond_pagto->di_search($sort);
  }

  function obter_cond_pagto()
  {
    return $this->o_cond_pagto->di_get();
  }
  
  function obter_cpp()
  {
    return $this->o_cpp->di_get();
  }

  function remover()
  {
    $this->o_cond_pagto->di_del();
    return $this->o_cond_pagto->{$this->o_cond_pagto->_field_id};
    
    foreach((array)$this->obter_cpp() AS $row)
    {
      $this->o_cpp->di_reset();
      $this->o_cpp->di_set($row);
      $this->o_cpp->di_del();
    }
   }
}


/* -----------------------------------------------------------------------------
 * Orcamento .
 * ---------------------------------------------------------------------------*/
 
class Orcamento
{
  var $o_orcamento;
  var $o_orcamento_prd;
  var $o_orcamento_opv;

  function Orcamento($id_orc=NULL)
  {
    $this->o_orcamento = new _orcamento($id_orc);
    $this->o_orcamento_opv = new _orcamento_customizado();
  }

  function gravar($a_orc=array(),$a_prd=array(),$a_cmp=array())
  {
    // add na tab orcamento .
    $this->o_orcamento->di_set($a_orc);
    if($a_orc[$this->o_orcamento->_field_id]==NULL)$this->o_orcamento->di_add();
    else $this->o_orcamento->di_upd();

    $_tmp_opv = new _orcamento_customizado();
    foreach((array)$this->obter_opv() AS $row)
    {
      $_tmp_opv->di_reset();
      $_tmp_opv->di_set($row);
      $_tmp_opv->di_del();
    }
    
    // add na tab produto_customizado .
    foreach((array)$a_prd AS $row)
    {
      if(count($row)>0)
      {
        $row[$this->o_orcamento->_field_id]=$this->o_orcamento->{$this->o_orcamento->_field_id};
        $this->o_orcamento_opv->di_reset();
        $this->o_orcamento_opv->di_set($row);
        $this->o_orcamento_opv->di_add();
      }
    }
    return $this->o_orcamento->{$this->o_orcamento->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_orcamento->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_orcamento->di_all();
  }

  function total()
  {
    return $this->o_orcamento->di_count();
  }

  function buscar($a_orc=array(),$sort=NULL)
  {
    $this->o_orcamento->di_set($a_orc);
    return $this->o_orcamento->di_search($sort);
  }

  function obter_orcamento()
  {
    return $this->o_orcamento->di_get();
  }

  function obter_opv()
  {
    return $this->o_orcamento_opv->di_all(array($this->o_orcamento->_field_id=>$this->o_orcamento->{$this->o_orcamento->_field_id}));
  }
  
  function obter_ioc($ioc_start)
  {
    $a_ioc = array();
    for($_i=$ioc_start;$_i<($ioc_start+8);$_i++)
    {
      $_a = $this->o_orcamento_opv->di_all(array('id'=>$_i));
      $a_ioc[($_a[0]['qtd_atacado'])] = $_a[0]['venda_un'];
    }
    return $a_ioc;
  }

  function remover()
  {
    $this->o_orcamento->di_del();

    foreach((array)$this->obter_opv() AS $row)
    {
      $this->o_orcamento_opv->di_reset();
      $this->o_orcamento_opv->di_set($row);
      $this->o_orcamento_opv->di_del();
    }
    return $this->o_orcamento->{$this->o_orcamento->_field_id};
  }
}

/* -----------------------------------------------------------------------------
 * Pedido.
 * ---------------------------------------------------------------------------*/

class Pedido
{
  var $o_pedido;
  var $o_pedido_pcs;

  function Pedido($id_ped=NULL)
  {
    $this->o_pedido = new _pedido($id_ped);
    $this->o_pedido_pcs = new _pedido_customizado();
  }

  function gravar($a_ped=array(),$a_prd=array())
  {
    # add na tab de pedidos ..
    $this->o_pedido->di_set($a_ped);
    if($a_ped[$this->o_pedido->_field_id]==NULL) $this->o_pedido->di_add();
    else $this->o_pedido->di_upd();
    
    # apaga registros antigos .
    $_tmp_pcs = new _pedido_customizado();
    foreach((array)$this->obter_pcs() AS $row)
    {
      $_tmp_pcs->di_reset();
      $_tmp_pcs->di_set($row);
      $_tmp_pcs->di_del();
    }
    # adiciona novos .
    foreach((array)$a_prd AS $row)
    {
      if(count($row)>0)
      {
        $row[$this->o_pedido->_field_id]=$this->o_pedido->{$this->o_pedido->_field_id};
        $this->o_pedido_pcs->di_reset();
        $this->o_pedido_pcs->di_set($row);
        $this->o_pedido_pcs->di_add();
      }
    }
    return $this->o_pedido->{$this->o_pedido->_field_id};
  }

  function listar($nrows=NULL,$offset=NULL,$sort=NULL)
  {
    return $this->o_pedido->di_list($nrows,$offset,$sort);
  }

  function listar_tudo()
  {
    return $this->o_pedido->di_all();
  }

  function total()
  {
    return $this->o_pedido->di_count();
  }

  function buscar($a_ped=array(),$sort=NULL)
  {
    $this->o_pedido->di_set($a_ped);
    return $this->o_pedido->di_search($sort);
  }
  
  function obter_pedido()
  {
    return $this->o_pedido->di_get();
  }

  function obter_pcs()
  {
    return $this->o_pedido_pcs->di_all(array($this->o_pedido->_field_id=>$this->o_pedido->{$this->o_pedido->_field_id}));
  }

  function obter_ped_por_orc($orc)
  {
    $_a = $this->o_pedido->di_all(array('id_orcamento'=>$orc));
    if(count($_a)==1) $this->o_pedido->di_set($_a[0]);
    return $this->obter_pedido();
  }

  function remover()
  {
    $this->o_pedido->di_del();
    
    # remove pcs
    foreach((array)$this->obter_pcs() AS $row)
    {
      $this->o_pedido_pcs->di_reset();
      $this->o_pedido_pcs->di_set($row);
      $this->o_pedido_pcs->di_del();
    }
    
    return $this->o_pedido->{$this->o_pedido->_field_id};
  }
}

// }}}

?>
