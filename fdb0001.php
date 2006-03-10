<?php

/**
 * FIX DATABASE 0001
 */

require './config.php';

$s_query = '
SELECT   componente_tabela.*
FROM     produto, produto_componente, componente_tabela
WHERE    produto.referencia >= 411
AND      produto.referencia <= 800
AND      produto.id_produto = produto_componente.id_produto
AND      produto_componente.id_componente = componente_tabela.id_componente
ORDER BY componente_tabela.id_componente';

$o_rs=$o_adodb->Execute($s_query);
$_a_results = array();

if($o_rs->RecordCount()>0)
{
  while(!$o_rs->EOF) $_a_results[]=$o_rs->FetchRow();
}

$_current_id_componente = 0;
$_current_componente_count = 0;
$_s_query_update = NULL;

for($i=0;$i<count($_a_results);$i++)
{
    if($_current_id_componente != $_a_results[$i]['id_componente'])
    {
        $_current_id_componente = $_a_results[$i]['id_componente'];
        $_current_componente_count = 0;
    }
    $_current_componente_count += 1;
    
    if($_current_componente_count == 1)
    {
        $_s_query_update = '
        UPDATE componente_tabela
        SET    quantidade_de  = 1,
               quantidade_ate = 1000,
               valor_max      = \'S\'
        WHERE  id = ' . $_a_results[$i]['id'];
    }
    elseif($_current_componente_count == 2)
    {
        $_s_query_update = '
        UPDATE componente_tabela
        SET    quantidade_de  = 1001,
               quantidade_ate = 0,
               custo_un       = '. $_a_results[($i - 1)]['custo_un'] .',
               valor_max      = \'N\'
        WHERE  id = ' . $_a_results[$i]['id'];
    }
    else
    {
        $_s_query_update = NULL;
    }
    
    if ($_s_query_update !== NULL) echo $_s_query_update .";<br>\n";
}

?>
