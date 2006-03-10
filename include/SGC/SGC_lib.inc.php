<?php

/**
 * <b><u>Sistema de Gerenciamento Comercial</u></b>
 * <p>Biblioteca de funções (SGC_lib.inc.php)</p>
 * @author Rafael Moraes
 */

// {{{ FUNÇÕES .

function gen_tmp_id($size=16)
{
  for($i=0;$i<$size;$i++) $_s .= chr(rand(65,90));
  return $_s;
}

function new_prd_data_list()
{
  global $CT_a_qtd_atacado;
  
  $a_data_list = array();
  $a_data_list['form'] = array();
  $a_data_list['componente'] = array();
  $a_data_list['custo'] = array_fill(0,10,array('nome'=>'','quantidade'=>0,'custo'=>'0.00','qtd_intervalo'=>''));
  $a_data_list['ppv'] = array();

  foreach((array)($CT_a_qtd_atacado) AS $k=>$v)
  {
    $a_data_list['ppv'][] = array
    (
      'qtd_atacado'      => $k,
      'percentual_venda' => '100.0',
      'venda_un'         => '0.00'
    );
  }
  return $a_data_list;
}

function new_cmp_data_list()
{
  return array_fill(0,10,array('quantidade_de'=>0,'quantidade_ate'=>0,'custo_un'=>'0.000'));
}

function new_orc_data_list()
{
  $a_data_list = array();
  $a_data_list['form'] = array();
  $a_data_list['produto'] = array();
  return $a_data_list;
}

function ppv_sort($a, $b)
{
  if(is_array($a) && is_array($b))
  {
    if ($a['qtd_atacado'] == $b['qtd_atacado']) return 0;
    return ($a['qtd_atacado'] < $b['qtd_atacado']) ? -1 : 1;
  }
  else
  {
    if($a == $b) return 0;
    return ($a < $b) ? -1 : 1;
  }
}

function diff_ids($a, $b)
{
  foreach((array)$a AS $v) $_a[]=$v['id'];
  foreach((array)$b AS $v) $_b[]=$v['id'];
  if(count($_a)>count($_b))
  {
    $_ap = $_a;
    $_as = $_b;
  }
  else
  {
    $_ap = $_b;
    $_as = $_a;
  }
  foreach((array)$_ap AS $v) if(in_array($v,(array)$_as)==FALSE) $_ar[] = $v;
  return $_ar;
}

function in_qtd_range($int)
{
  global $CT_a_qtd_atacado;
  $_a_qtd = array_keys($CT_a_qtd_atacado);
  for($i=0;$i<count($_a_qtd);$i++)
  {
    $val = 0;
    $cur = (int)$_a_qtd[$i];
    $nxt = (int)$_a_qtd[($i+1)];
    $prv = (int)$_a_qtd[($i-1)];
    
    if($int>$prv && $int<$nxt) return $cur;
  }
  return $cur;
}



// }}}

// {{{ FILTROS .

function str2int($str)
{
  if(preg_match("/^-.*$/",$str))
  return "-". preg_replace("/[^0-9]/","",$str);
  else
  return preg_replace("/[^0-9]/","",$str);
}

function str2float($str)
{
  $sep=preg_replace("/[0-9]*/","",$str);
  if(strlen($sep)==0) return $str;
  elseif(strlen($sep)==1) return str_replace($sep,".",$str);
  elseif(strlen($sep)==2) return str_replace($sep{1},".",str_replace($sep{0},"",$str));
}

function data2date($str)
{
  $_a=split("-",preg_replace("/[^0-9]/","-",$str));
  if(count($_a)==3) return $_a[2]."-".$_a[1]."-".$_a[0];
  else return $str;
}

function date2data($str)
{
  $_a=split("-",preg_replace("/[^0-9]/","-",$str));
  if(count($_a)==3) return $_a[2]."/".$_a[1]."/".$_a[0];
  else return $str;
}

function latin_ucase($str)
{
  return strtr($str, "abcdefghijklmnopqrstuvwxyzàáâãäåçèéêëìíîïñòóôõöùúûüý",
                     "ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ");
}

function money_fmt($str)
{
  return number_format(round($str,2),2);
}

// }}}

?>
