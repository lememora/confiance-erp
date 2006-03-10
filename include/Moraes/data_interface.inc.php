<?php

/**
 * <p>Classe data_interface (data_interface.inc.php)</p>
 * @author Rafael Moraes
 * @version 1.4
 * @require ADODB
 *
 * null    di_init   (string)
 * null    di_set    (array)
 * null    di_reset  (null)
 * array   di_get    (null)
 * null    di_load   (null)
 * array   di_search (string)
 * array   di_list   ([int],[int],[string]) // NAS PROXIMAS VERSOES, di_list aceitara WHERE e tomara o lugar de di_all
 * array   di_all    ([array],[string])
 * int     di_count  (null)
 * boolean di_add    (null)
 * boolean di_del    (null)
 * boolean di_upd    (null)
 */

class data_interface
{
  var $_table;              // TABELA NA BD
  var $_fields=array();     // CAMPOS DA TABELA NA BD
  var $_field_id;           // CAMPO CORRESPONDENTE À PK DA TABELA
  var $_filters=array();    // FILTROS PARA INSERÇÃO DE DADOS
  var $_b_data_isset=FALSE; // OS DADOS FORAM CARREGADOS?

  /**
   * Inicializa a partir de um registro
   * @param string Primary Key
   */
  function di_init($id)
  {
    if($id!==NULL)
    {
      $this->{$this->_field_id}=$id;
      $this->di_load();
    }
  }

  /**
   * Define os dados
   * @param array Dados
   */
  function di_set(&$a_data,$no_filters=FALSE)
  {
    if(count($a_data)>0)
    {
      foreach($this->_fields AS $f)
      {
        if($a_data[$f]!==NULL)
        {
          if($this->_filters[$f]!==NULL && $no_filters==FALSE)
          $this->$f=$this->_filters[$f]($a_data[$f]);
          else
          $this->$f=$a_data[$f];
        }
      }
      $this->_b_data_isset=TRUE;
    }
  }

  /**
   * Limpa os dados
   */
  function di_reset()
  {
    foreach($this->_fields AS $f) unset($this->$f);
    $this->_b_data_isset=FALSE;
  }

  /**
   * Obtém os dados
   * @return array
   */
  function di_get()
  {
    if($this->_b_data_isset===TRUE)
    {
      $a_data=array();
      foreach($this->_fields AS $f)
      {
        if($this->$f!==NULL) $a_data[$f]=$this->$f;
      }
    }
    return $a_data;
  }

  /*
   * Executa uma consulta no BD
   */
  function di_exec($s_qry)
  {
    global $o_adodb;

    $o_rs=$o_adodb->Execute($s_qry);
    $_a_results = array();

    if($o_rs->RecordCount()>0)
    {
      while(!$o_rs->EOF) $_a_results[]=$o_rs->FetchRow();
    }
    return $_a_results;
  }

  /**
   * Obtém um registro da BD
   * @return boolean
   */
  function di_load()
  {
    global $o_adodb;

    $o_rs=$o_adodb->Execute('SELECT * FROM '.$this->_table.' WHERE '.$this->_field_id.'="'.$this->{$this->_field_id}.'"');

    if($o_rs->RecordCount()>0)
    {
      $this->di_reset();
      $this->di_set($o_rs->fields,TRUE);
      return TRUE;
    }
  }

  /**
   * Executa busca na BD
   * @param  string Ordenação
   * @return array
   */
  function di_search($sort=NULL)
  {
    global $o_adodb;

    // BUSCA REGISTROS
    $_a_results=array();
    $a_bind=&$this->di_get();

    foreach((array)($a_bind) AS $field=>$value)
    {
      if($value!=NULL)
      {
        if($field==$this->_field_id || $value==preg_replace('/[^0-9]/','',$value))
        {
          $o_rs=$o_adodb->Execute('SELECT * FROM '.$this->_table.' WHERE '.$field.'="'.$value.'"');
        }
        else
        {
          $o_rs=$o_adodb->Execute('SELECT * FROM '.$this->_table.' WHERE LOWER('.$field.') LIKE "%'.strtolower($value).'%"');
        }
        
        if($o_rs->RecordCount()>0)
        {
          while(!$o_rs->EOF) $_a_results[]=$o_rs->FetchRow();
        }
      }
    }

    // REMOVE REGISTROS DUPLICADOS E GERA ARRAY DE ORDENAÇÃO
    if($sort===NULL) $sort=$this->_field_id;
    $__a_results=array();
    $_a_sort_res=array();

    for($i=0;$i<count($_a_results);$i++)
    {
      $_pk=$_a_results[$i][$this->_field_id];

      if($_a_sort_res[$_pk]===NULL)
      {
        $__a_results[$_pk]=$_a_results[$i];
        $_a_sort_res[$_pk]=strtolower($_a_results[$i][$sort]);
      }
    }

    // ORDENA PK
    $_a_results=array();
    @asort($_a_sort_res, SORT_REGULAR);
    $_a_sort_res=@array_keys($_a_sort_res);

    // GERA RESULTADO ORDENADO
    for($i=0;$i<count($_a_sort_res);$i++)
    {
      $_a_results[]=$__a_results[($_a_sort_res[$i])];
    }

    // CASO ENCONTRE APENAS UM REGISTRO, DEFINE AS VARIAVEIS .
    if(count($_a_results)==1)
    {
      $this->di_reset();
      $this->di_set($_a_results[0],TRUE);
    }
    
    return $_a_results;
  }

  /**
   * Carrega lista paginada da base de dados
   * @return array
   */
  function di_list($nrows=100,$offset=-1,$sort=NULL)
  {
    global $o_adodb;
    $__a = preg_split("/\ /",$sort);
    if(in_array($__a[0],$this->_fields)==FALSE) $sort=$this->_field_id;
    $o_rs=$o_adodb->SelectLimit('SELECT * FROM '.$this->_table.' ORDER BY '.$sort, $nrows, $offset);

    if($o_rs->RecordCount()>0)
    {
      $a_results=array();
      while(!$o_rs->EOF) $a_results[]=$o_rs->FetchRow();
    }
    return $a_results;
  }

  /**
   * Carrega lista completa de dados do BD
   * @return array
   */
  function di_all($where=array(),$sort=NULL)
  {
    global $o_adodb;

    if($sort==NULL) $sort=$this->_field_id;
    $s_query = 'SELECT * FROM '.$this->_table.' WHERE 1=1 ';
    foreach($where AS $k=>$v) $s_query .= ' AND '.$k.'="'.$v.'" ';
    $s_query .= 'ORDER BY '.$sort;
    
    $o_rs=$o_adodb->Execute($s_query);

    if($o_rs->RecordCount()>0)
    {
      $a_results=array();
      while(!$o_rs->EOF) $a_results[]=$o_rs->FetchRow();
    }
    return $a_results;
  }

  /**
   * Conta o número de registros na BD
   * @return array
   */
  function di_count()
  {
    global $o_adodb;

    $o_rs=$o_adodb->Execute('SELECT COUNT(*) AS TOTAL FROM '.$this->_table);

    if($o_rs->RecordCount()>0)
    {
      $a_results=$o_rs->FetchRow();
      return $a_results['TOTAL'];
    }
  }

  /**
   * Adiciona um registro à BD
   * @return boolean
   */
  function di_add()
  {
    global $o_adodb;

    if($this->_b_data_isset)
    {
      $o_null_rs=$o_adodb->Execute('SELECT * FROM '.$this->_table.' WHERE '.$this->_field_id.'=-1');
      $a_bind=&$this->di_get();
      $o_rs=$o_adodb->Execute($o_adodb->GetInsertSQL($o_null_rs, $a_bind));

      if($o_adodb->Insert_ID())
      {
        $this->{$this->_field_id}=$o_adodb->Insert_ID();
        return TRUE;
      }
    }
  }

  /**
   * Remove um registro da BD
   * @return boolean
   */
  function di_del()
  {
    global $o_adodb;

    if($this->_b_data_isset)
    {
      $o_adodb->Execute('DELETE FROM '.$this->_table.' WHERE '.$this->_field_id.'="'.$this->{$this->_field_id}.'"');
      return TRUE;
    }
  }

  /**
   * Atualiza um registro da BD
   * @return boolean
   */
  function di_upd()
  {
    global $o_adodb;

    if($this->_b_data_isset)
    {
      $o_cur_rs=$o_adodb->Execute('SELECT * FROM '.$this->_table.' WHERE '.$this->_field_id.'="'.$this->{$this->_field_id}.'"');
      $a_bind=&$this->di_get();
      $o_rs=$o_adodb->Execute($o_adodb->GetUpdateSQL($o_cur_rs, $a_bind));
      return TRUE;
    }
  }

}

?>
