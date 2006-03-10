<?php

/**
 * FIX DATABASE 0001
 */

require './config.php';

$s_query = '
SELECT produto_componente.*
FROM produto_componente, produto
WHERE produto_componente.id_produto = produto.id_produto
AND produto.referencia >= 411
AND produto.referencia <= 800
ORDER BY produto_componente.id_componente';

$o_rs=$o_adodb->Execute($s_query);
$_a_results = array();

if($o_rs->RecordCount()>0)
{
  while(!$o_rs->EOF) {
      $_a_results=$o_rs->FetchRow();
      $_s_update_query = '
      UPDATE produto_componente
      SET    nome = \'GRAVAÇÃO SILK\'
      WHERE  id_componente = '. $_a_results['id_componente'];
      echo $_s_update_query .";<br>\n";
  }
}

?>
