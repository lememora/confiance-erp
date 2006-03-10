<?php

/**
 * <b><u>Sistema de Gerenciamento Comercial</u></b>
 * <p>Classes de tabelas SGC (SGC_tabcl.inc.php)</p>
 * @author Rafael Moraes
 */


// {{{ CLASSES DE TABELAS .


/**
 * Cliente .
 */
class _cliente extends data_interface
{
  var $id_cliente;
  var $id_usuario;
  var $fantasia;
  var $email;
  var $razao_social;
  var $endereco;
  var $bairro;
  var $cidade;
  var $uf;
  var $cep;
  var $cnpj_cpf;
  var $ie_rg;
  var $telefone_1;
  var $telefone_2;
  var $telefone_fax;
  var $contato;
  var $dt_contato;
  var $niv_satisfacao;

  function _cliente($id=NULL)
  {
    $this->_table='cliente';
    $this->_field_id='id_cliente';
    $this->_fields = array
    (
      'id_cliente','id_usuario','fantasia','email','razao_social','endereco',
      'bairro','cidade','uf','cep','cnpj_cpf','ie_rg','telefone_1','telefone_2',
      'telefone_fax','contato','dt_contato','niv_satisfacao'
    );
    $this->_filters = array
    (
      'fantasia'=>'latin_ucase','email'=>'latin_ucase',
      'razao_social'=>'latin_ucase','endereco'=>'latin_ucase',
      'bairro'=>'latin_ucase','cidade'=>'latin_ucase','uf'=>'latin_ucase',
      'cep'=>'str2int','cnpj_cpf'=>'str2int','ie_rg'=>'latin_ucase',
      'telefone_1'=>'latin_ucase','telefone_2'=>'latin_ucase',
      'telefone_fax'=>'latin_ucase','contato'=>'latin_ucase',
      'dt_contato'=>'data2date','niv_satisfacao'=>'latin_ucase'
    );
    $this->di_init($id);
  }
}

/**
 * Componente Tabela     .
 */
class _componente_tabela extends data_interface
{
  var $id;
  var $id_componente;
  var $quantidade_de;
  var $quantidade_ate;
  var $custo_un;
  var $valor_max;

  function _componente_tabela($id=NULL)
  {
    $this->_table='componente_tabela';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_componente','quantidade_de','quantidade_ate','custo_un','valor_max'
    );
    $this->_filters = array
    (
      'quantidade_de'=>'str2int','quantidade_ate'=>'str2int','custo_un'=>'str2float'
    );
    $this->di_init($id);
  }
}

/**
 * Empresa     .
 */
class _empresa extends data_interface
{
  var $id_empresa;
  var $nome;
  var $codigo_exib;
  var $ativo;

  function _empresa($id=NULL)
  {
    $this->_table='empresa';
    $this->_field_id='id_empresa';
    $this->_fields = array
    (
      'id_empresa','nome','codigo_exib','ativo'
    );
    $this->_filters = array();
    $this->di_init($id);
  }
}

/**
 * Mídia     .
 */
class _midia extends data_interface
{
  var $id_midia;
  var $nome;

  function _midia($id=NULL)
  {
    $this->_table='midia';
    $this->_field_id='id_midia';
    $this->_fields = array
    (
      'id_midia','nome'
    );
    $this->_filters = array();
    $this->di_init($id);
  }
}

/**
 * Orçamento     .
 */
class _orcamento extends data_interface
{
  var $id_orcamento;
  var $id_cliente;
  var $id_usuario;
  var $id_empresa;
  var $id_midia;
  var $id_cond_pagto;
  var $dt_emissao;
  var $prazo_entrega;
  var $custo_base;
  var $observacao;
  var $gravacao;
  var $cor;
  var $aprovado;

  function _orcamento($id=NULL)
  {
    $this->_table='orcamento';
    $this->_field_id='id_orcamento';
    $this->_fields=array('id_orcamento','id_cliente','id_usuario','id_empresa','id_midia','id_cond_pagto','dt_emissao','prazo_entrega','custo_base','observacao','gravacao','cor','aprovado');
    $this->_filters=array ('dt_emissao'=>'data2date','custo_base'=>'str2float','observacao'=>'latin_ucase','gravacao'=>'latin_ucase','cor'=>'latin_ucase');
    $this->di_init($id);
  }
}

/**
 * Orçamento Produto    .
 */
class _orcamento_produto extends data_interface
{
  var $id;
  var $id_orcamento;
  var $id_produto;
  var $quantidade;

  function _orcamento_produto($id=NULL)
  {
    $this->_table='orcamento_produto';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_orcamento','id_produto','quantidade'
    );
    $this->_filters = array();
    $this->di_init($id);
  }
}

/**
 * Orçamento Customizado     .
 */
class _orcamento_customizado extends data_interface
{
  var $id;
  var $id_orcamento;
  var $id_produto;
  var $qtd_atacado;
  var $percentual_venda;
  var $venda_un;

  function _orcamento_customizado($id=NULL)
  {
    $this->_table='orcamento_customizado';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_orcamento','id_produto','qtd_atacado','percentual_venda','venda_un'
    );
    $this->_filters = array
    (
      'venda_un'=>'str2float'
    );
    $this->di_init($id);
  }
}

/**
 * Pedido     .
 */
class _pedido extends data_interface
{
  var $id_pedido;
  var $id_orcamento;
  var $id_cliente;
  var $id_usuario;
  var $id_midia;
  var $id_cond_pagto;
  var $dt_emissao;
  var $prazo_entrega;
  var $custo_base;
  var $entregue;
  var $dt_entrega;
  var $valor;
  var $observacao;
  var $gravacao;
  var $cor;
  var $forbid;

  function _pedido($id=NULL)
  {
    $this->_table='pedido';
    $this->_field_id='id_pedido';
    $this->_fields = array('id_pedido','id_orcamento','id_cliente','id_usuario','id_midia','id_cond_pagto','dt_emissao','prazo_entrega','custo_base','entregue','dt_entrega','valor','observacao','gravacao','cor','forbid');
    $this->_filters=array ('dt_emissao'=>'data2date','dt_entrega'=>'data2date','custo_base'=>'str2float','observacao'=>'latin_ucase','gravacao'=>'latin_ucase','cor'=>'latin_ucase');
    $this->di_init($id);
  }
}

/**
 * Pedido Customizado     .
 */
class _pedido_customizado extends data_interface
{
  var $id;
  var $id_pedido;
  var $id_produto;
  var $ioc;
  var $quantidade;
  var $venda_un;

  function _pedido_customizado($id=NULL)
  {
    $this->_table='pedido_customizado';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_pedido','id_produto','ioc','quantidade','venda_un'
    );
    $this->_filters = array
    (
      'quantidade'=>'str2int','venda_un'=>'str2float'
    );
    $this->di_init($id);
  }
}

/**
 * Produto     .
 */
class _produto extends data_interface
{
  var $id_produto;
  var $referencia;
  var $custo_base;
  var $descricao;
  var $un_medida;
  var $controle_estoque;
  var $estoque;
  var $observacao;

  function _produto($id=NULL)
  {
    $this->_table='produto';
    $this->_field_id='id_produto';
    $this->_fields = array
    (
      'id_produto','referencia','custo_base','custo_un','descricao','un_medida','controle_estoque','estoque','observacao'
    );
    $this->_filters = array
    (
      'referencia'=>'latin_ucase','custo_base'=>'str2float','descricao'=>'latin_ucase','estoque'=>'str2int','observacao'=>'latin_ucase'
    );
    $this->di_init($id);
  }
}

/**
 * Produto Componente    .
 */
class _produto_componente extends data_interface
{
  var $id_componente;
  var $id_produto;
  var $nome;
  var $un_medida;
  var $quantidade;

  function _produto_componente($id=NULL)
  {
    $this->_table='produto_componente';
    $this->_field_id='id_componente';
    $this->_fields = array
    (
      'id','id_produto','id_componente','nome','un_medida','quantidade'
    );
    $this->_filters = array
    (
      'nome'=>'latin_ucase','quantidade'=>'str2int'
    );
    $this->di_init($id);
  }
}

/**
 * Produto Custo    .
 */
class _produto_custo extends data_interface
{
  var $id;
  var $id_produto;
  var $nome;
  var $quantidade;
  var $custo;
  var $qtd_intervalo;

  function _produto_custo($id=NULL)
  {
    $this->_table='produto_custo';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_produto','nome','quantidade','custo','qtd_intervalo'
    );
    $this->_filters = array
    (
      'nome'=>'latin_ucase','quantidade'=>'str2int','custo'=>'str2float'
    );
    $this->di_init($id);
  }
}

/**
 * Produto Percentual de Venda     .
 */
class _produto_percentual_venda extends data_interface
{
  var $id;
  var $id_produto;
  var $qtd_atacado;
  var $percentual_venda;

  function _produto_percentual_venda($id=NULL)
  {
    $this->_table='produto_percentual_venda';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_produto','qtd_atacado','percentual_venda'
    );
    $this->_filters = array
    (
      'qtd_atacado'=>'str2int','percentual_venda'=>'str2float'
    );
    $this->di_init($id);
  }
}

/**
 * Recebimento    .
 */
class _recebimento extends data_interface
{
  var $id_recebimento;
  var $id_empresa;
  var $id_cliente;
  var $id_pedido;
  var $id_cond_pagto;
  var $id_parcela;
  var $dt_emissao;
  var $dt_pagamento;
  var $dt_vencimento;
  var $forma_pagto;
  var $valor;
  var $recebido;
  var $valor_pago;
  var $valor_juros;
  var $valor_desconto;
  var $n_documento;

  function _recebimento($id=NULL)
  {
    $this->_table='recebimento';
    $this->_field_id='id_recebimento';
    $this->_fields = array
    (
      'id_recebimento','id_empresa','id_cliente','id_pedido','id_cond_pagto','id_parcela','dt_emissao','dt_pagamento','dt_vencimento','forma_pagto','valor','recebido','valor_pago','valor_juros','valor_desconto','n_documento'
    );
    $this->_filters = array ();
    $this->di_init($id);
  }
}

/**
 *  Condição de pagamento   .
 */
class _cond_pagto extends data_interface
{
  var $id_cond_pagto;
  var $nome;
  var $forma_pagto;

  function _cond_pagto($id=NULL)
  {
    $this->_table='cond_pagto';
    $this->_field_id='id_cond_pagto';
    $this->_fields = array
    (
      'id_cond_pagto','nome','forma_pagto'
    );
    $this->_filters = array ();
    $this->di_init($id);
  }
}

/**
 *  Condição de pagamento - parcela   .
 */
class _cond_pagto_parcela extends data_interface
{
  var $id;
  var $id_cond_pagto;
  var $id_parcela;
  var $dias;

  function _cond_pagto($id=NULL)
  {
    $this->_table='cond_pagto_parcela';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_cond_pagto','id_parcela','dias'
    );
    $this->_filters = array ();
    $this->di_init($id);
  }
}

/**
 *  asdfasdfasdfasdf  .
 */
class _orcamento_prd_cmp extends data_interface
{
  var $id;
  var $id_orcamento;
  var $id_produto;
  var $id_componente;
  var $quantidade;

  function _orcamento_prd_cmp($id=NULL)
  {
    $this->_table='orcamento_prd_cmp';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_orcamento','id_produto','id_componente','quantidade'
    );
    $this->_filters = array ();
    $this->di_init($id);
  }
}

/**
 *  asdfasdfasdfasdf  2.
 */
class _orcamento_prd_cst extends data_interface
{
  var $id;
  var $id_orcamento;
  var $id_produto;
  var $id_custo;
  var $quantidade;

  function _orcamento_prd_cst($id=NULL)
  {
    $this->_table='orcamento_prd_cst';
    $this->_field_id='id';
    $this->_fields = array
    (
      'id','id_orcamento','id_produto','id_custo','quantidade'
    );
    $this->_filters = array ();
    $this->di_init($id);
  }
}


// }}}

?>
