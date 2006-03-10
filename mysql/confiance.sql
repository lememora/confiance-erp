# phpMyAdmin MySQL-Dump
# version 2.3.2
# http://www.phpmyadmin.net/ (download page)
#
# Servidor: localhost
# Tempo de Generação: Out 27, 2004 at 03:07 PM
# Versão do Servidor: 4.00.00
# Versão do PHP: 4.2.3
# Banco de Dados : `confiance`
# --------------------------------------------------------

#
# Estrutura da tabela `cliente`
#

CREATE TABLE cliente (
  id_cliente mediumint(8) unsigned NOT NULL auto_increment,
  id_usuario char(20) NOT NULL default '',
  fantasia char(100) default NULL,
  email char(100) default NULL,
  razao_social char(100) default NULL,
  endereco char(200) default NULL,
  bairro char(50) default NULL,
  cidade char(50) default NULL,
  uf char(2) default NULL,
  cep int(8) unsigned zerofill default NULL,
  cnpj_cpf char(20) default NULL,
  ie_rg char(50) default NULL,
  telefone_1 char(50) default NULL,
  telefone_2 char(50) default NULL,
  telefone_fax char(50) default NULL,
  contato char(50) default NULL,
  dt_contato date default NULL,
  niv_satisfacao char(10) default NULL,
  PRIMARY KEY  (id_cliente),
  KEY id_usuario (id_usuario)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `cliente`
#

# --------------------------------------------------------

#
# Estrutura da tabela `cliente_tmp_`
#

CREATE TABLE cliente_tmp_ (
  CODIGO varchar(255) NOT NULL default '',
  FANTASIA varchar(255) NOT NULL default '',
  RAZAO_SOCIAL varchar(255) NOT NULL default '',
  ENDERECO varchar(255) NOT NULL default '',
  BAIRRO varchar(255) NOT NULL default '',
  CIDADE varchar(255) NOT NULL default '',
  UF varchar(255) NOT NULL default '',
  CEP varchar(255) NOT NULL default '',
  CGC_CPF varchar(255) NOT NULL default '',
  IE_RG varchar(255) NOT NULL default '',
  EMAIL varchar(255) NOT NULL default '',
  REPRESENTANTE varchar(255) NOT NULL default '',
  CONTATO varchar(255) NOT NULL default '',
  ANIVERSARIO_CONT varchar(255) NOT NULL default '',
  SATISFACAO varchar(255) NOT NULL default '',
  TELEFONE1 varchar(255) NOT NULL default '',
  TELEFONE2 varchar(255) NOT NULL default '',
  FAX varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Extraindo dados da tabela `cliente_tmp_`
#

# --------------------------------------------------------

#
# Estrutura da tabela `componente_tabela`
#

CREATE TABLE componente_tabela (
  id int(10) unsigned NOT NULL auto_increment,
  id_componente mediumint(8) unsigned NOT NULL default '0',
  quantidade_de int(10) unsigned NOT NULL default '0',
  quantidade_ate int(10) unsigned NOT NULL default '0',
  custo_un float(5,3) unsigned NOT NULL default '0.000',
  valor_max enum('S','N') NOT NULL default 'N',
  PRIMARY KEY  (id),
  KEY id_componente (id_componente)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `componente_tabela`
#

# --------------------------------------------------------

#
# Estrutura da tabela `cond_pagto`
#

CREATE TABLE cond_pagto (
  id_cond_pagto smallint(5) unsigned NOT NULL auto_increment,
  nome char(100) default NULL,
  forma_pagto char(20) default NULL,
  PRIMARY KEY  (id_cond_pagto)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `cond_pagto`
#

# --------------------------------------------------------

#
# Estrutura da tabela `cond_pagto_parcela`
#

CREATE TABLE cond_pagto_parcela (
  id int(10) unsigned NOT NULL auto_increment,
  id_cond_pagto mediumint(8) unsigned NOT NULL default '0',
  id_parcela smallint(5) unsigned NOT NULL default '0',
  dias smallint(5) unsigned default NULL,
  PRIMARY KEY  (id),
  KEY id_cond_pagto (id_cond_pagto),
  KEY id_parcela (id_parcela)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `cond_pagto_parcela`
#

# --------------------------------------------------------

#
# Estrutura da tabela `empresa`
#

CREATE TABLE empresa (
  id_empresa smallint(5) unsigned NOT NULL auto_increment,
  nome char(100) default NULL,
  codigo_exib char(10) default NULL,
  ativo enum('S','N') NOT NULL default 'S',
  PRIMARY KEY  (id_empresa),
  KEY ativo (ativo)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `empresa`
#

# --------------------------------------------------------

#
# Estrutura da tabela `midia`
#

CREATE TABLE midia (
  id_midia smallint(5) unsigned NOT NULL auto_increment,
  nome char(100) default NULL,
  PRIMARY KEY  (id_midia)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `midia`
#

# --------------------------------------------------------

#
# Estrutura da tabela `orcamento`
#

CREATE TABLE orcamento (
  id_orcamento int(10) unsigned NOT NULL auto_increment,
  id_cliente mediumint(8) unsigned NOT NULL default '0',
  id_usuario varchar(20) NOT NULL default '',
  id_empresa smallint(5) unsigned NOT NULL default '0',
  id_midia smallint(5) unsigned default NULL,
  id_cond_pagto smallint(5) unsigned default NULL,
  dt_emissao date default NULL,
  prazo_entrega varchar(20) default NULL,
  custo_base float(6,2) default NULL,
  observacao text,
  gravacao varchar(100) default NULL,
  cor varchar(100) default NULL,
  aprovado enum('S','N') NOT NULL default 'N',
  PRIMARY KEY  (id_orcamento),
  KEY id_cliente (id_cliente),
  KEY id_usuario (id_usuario),
  KEY id_empresa (id_empresa),
  KEY id_midia (id_midia),
  KEY dt_emissao (dt_emissao),
  KEY aprovado (aprovado)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `orcamento`
#

# --------------------------------------------------------

#
# Estrutura da tabela `orcamento_customizado`
#

CREATE TABLE orcamento_customizado (
  id int(10) unsigned NOT NULL auto_increment,
  id_orcamento int(10) unsigned NOT NULL default '0',
  id_produto int(10) unsigned NOT NULL default '0',
  qtd_atacado mediumint(8) unsigned default '0',
  percentual_venda float(3,1) unsigned NOT NULL default '100.0',
  venda_un float(5,3) default '0.000',
  PRIMARY KEY  (id),
  KEY id_orcamento (id_orcamento),
  KEY id_produto (id_produto)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `orcamento_customizado`
#

# --------------------------------------------------------

#
# Estrutura da tabela `orcamento_prd_cmp`
#

CREATE TABLE orcamento_prd_cmp (
  id int(10) unsigned NOT NULL auto_increment,
  id_orcamento int(10) unsigned NOT NULL default '0',
  id_produto int(10) unsigned NOT NULL default '0',
  id_componente int(10) unsigned NOT NULL default '0',
  quantidade mediumint(8) unsigned NOT NULL default '0',
  UNIQUE KEY id (id),
  KEY id_orcamento (id_orcamento),
  KEY id_produto (id_produto),
  KEY id_componente (id_componente)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `orcamento_prd_cmp`
#

# --------------------------------------------------------

#
# Estrutura da tabela `orcamento_tmp_`
#

CREATE TABLE orcamento_tmp_ (
  NUMERO varchar(255) NOT NULL default '',
  DATA varchar(255) NOT NULL default '',
  CLIENTE varchar(255) NOT NULL default '',
  VALIDADE varchar(255) NOT NULL default '',
  PREV_ENTREGA varchar(255) NOT NULL default '',
  COND_PGTO varchar(255) NOT NULL default '',
  VL_FOTOLITO varchar(255) NOT NULL default '',
  VENDEDOR varchar(255) NOT NULL default '',
  MIDIA varchar(255) NOT NULL default '',
  OBSERVACAO text NOT NULL,
  APROVADO varchar(255) NOT NULL default '',
  PEDIDO varchar(255) NOT NULL default '',
  USUARIO varchar(255) NOT NULL default '',
  GRAVACAO varchar(255) NOT NULL default '',
  COR varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Extraindo dados da tabela `orcamento_tmp_`
#

# --------------------------------------------------------

#
# Estrutura da tabela `orcamento_tmp_2`
#

CREATE TABLE orcamento_tmp_2 (
  ORDEN varchar(255) NOT NULL default '',
  ORCAMENTO varchar(255) NOT NULL default '',
  REF_PROD varchar(255) NOT NULL default '',
  VL_50 varchar(255) NOT NULL default '',
  VL_100 varchar(255) NOT NULL default '',
  VL_250 varchar(255) NOT NULL default '',
  VL_500 varchar(255) NOT NULL default '',
  VL_1000 varchar(255) NOT NULL default '',
  VL_2000 varchar(255) NOT NULL default '',
  VL_5000 varchar(255) NOT NULL default '',
  VL_10000 varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Extraindo dados da tabela `orcamento_tmp_2`
#

# --------------------------------------------------------

#
# Estrutura da tabela `pagamento`
#

CREATE TABLE pagamento (
  id_pagamento mediumint(8) unsigned NOT NULL auto_increment,
  id_pg_categoria smallint(5) unsigned NOT NULL default '0',
  nome varchar(100) default NULL,
  dt_pagamento date default NULL,
  dt_vencimento date default NULL,
  valor float(6,2) default NULL,
  observacao text,
  PRIMARY KEY  (id_pagamento),
  KEY id_pg_categoria (id_pg_categoria),
  KEY dt_pagamento (dt_pagamento),
  KEY dt_vencimento (dt_vencimento)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `pagamento`
#

# --------------------------------------------------------

#
# Estrutura da tabela `pedido`
#

CREATE TABLE pedido (
  id_pedido int(10) unsigned NOT NULL auto_increment,
  id_orcamento int(10) unsigned NOT NULL default '0',
  id_cliente mediumint(8) unsigned NOT NULL default '0',
  id_usuario varchar(20) NOT NULL default '',
  id_midia mediumint(5) unsigned NOT NULL default '0',
  id_cond_pagto mediumint(5) unsigned NOT NULL default '0',
  dt_emissao date default NULL,
  prazo_entrega varchar(20) default NULL,
  custo_base float(6,2) unsigned NOT NULL default '0.00',
  entregue enum('S','N') NOT NULL default 'N',
  dt_entrega date default NULL,
  valor float(6,2) default NULL,
  observacao text,
  gravacao varchar(100) default NULL,
  cor varchar(100) default NULL,
  forbid enum('S','N') NOT NULL default 'N',
  PRIMARY KEY  (id_pedido),
  KEY id_orcamento (id_orcamento),
  KEY id_usuario (id_usuario),
  KEY dt_emissao (dt_emissao),
  KEY entregue (entregue),
  KEY id_cliente (id_cliente),
  KEY id_midia (id_midia,id_cond_pagto)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `pedido`
#

# --------------------------------------------------------

#
# Estrutura da tabela `pedido_customizado`
#

CREATE TABLE pedido_customizado (
  id int(10) unsigned NOT NULL auto_increment,
  id_pedido int(10) unsigned NOT NULL default '0',
  ioc int(10) unsigned NOT NULL default '0',
  id_produto int(10) unsigned NOT NULL default '0',
  quantidade mediumint(8) unsigned NOT NULL default '0',
  venda_un float(5,3) default '0.000',
  PRIMARY KEY  (id),
  KEY id_pedido (id_pedido),
  KEY id_produto (id_produto),
  KEY ioc (ioc)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `pedido_customizado`
#

# --------------------------------------------------------

#
# Estrutura da tabela `pedido_tmp_`
#

CREATE TABLE pedido_tmp_ (
  NUMERO varchar(255) NOT NULL default '',
  DATA varchar(255) NOT NULL default '',
  CLIENTE varchar(255) NOT NULL default '',
  VALIDADE varchar(255) NOT NULL default '',
  PREV_ENTREGA varchar(255) NOT NULL default '',
  COND_PGTO varchar(255) NOT NULL default '',
  VL_FOTOLITO varchar(255) NOT NULL default '',
  VENDEDOR varchar(255) NOT NULL default '',
  MIDIA varchar(255) NOT NULL default '',
  OBSERVACAO text NOT NULL,
  VALOR_TOTAL varchar(255) NOT NULL default '',
  ORCAMENTO varchar(255) NOT NULL default '',
  USUARIO varchar(255) NOT NULL default '',
  ENTREGUE varchar(255) NOT NULL default '',
  DATA_ENTREGA varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Extraindo dados da tabela `pedido_tmp_`
#

# --------------------------------------------------------

#
# Estrutura da tabela `pedido_tmp_2`
#

CREATE TABLE pedido_tmp_2 (
  ORDEM varchar(255) NOT NULL default '',
  PEDIDO varchar(255) NOT NULL default '',
  REF_PROD varchar(255) NOT NULL default '',
  GRAVACAO varchar(255) NOT NULL default '',
  COR varchar(255) NOT NULL default '',
  QTE varchar(255) NOT NULL default '',
  VL_UNITARIO varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Extraindo dados da tabela `pedido_tmp_2`
#

# --------------------------------------------------------

#
# Estrutura da tabela `pg_categoria`
#

CREATE TABLE pg_categoria (
  id_pg_categoria smallint(5) unsigned NOT NULL auto_increment,
  nome char(100) default NULL,
  PRIMARY KEY  (id_pg_categoria)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `pg_categoria`
#

# --------------------------------------------------------

#
# Estrutura da tabela `produto`
#

CREATE TABLE produto (
  id_produto int(10) NOT NULL auto_increment,
  referencia varchar(20) NOT NULL default '',
  custo_base float(4,2) default '0.00',
  descricao text,
  un_medida varchar(10) default NULL,
  controle_estoque enum('S','N') NOT NULL default 'S',
  estoque int(10) default NULL,
  observacao text,
  PRIMARY KEY  (id_produto),
  KEY referencia (referencia)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `produto`
#

# --------------------------------------------------------

#
# Estrutura da tabela `produto_componente`
#

CREATE TABLE produto_componente (
  id_componente int(10) unsigned NOT NULL auto_increment,
  id_produto int(10) unsigned NOT NULL default '0',
  nome char(100) NOT NULL default '',
  un_medida char(10) NOT NULL default '',
  quantidade mediumint(8) unsigned NOT NULL default '1',
  PRIMARY KEY  (id_componente),
  KEY id_produto (id_produto)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `produto_componente`
#

# --------------------------------------------------------

#
# Estrutura da tabela `produto_custo`
#

CREATE TABLE produto_custo (
  id int(10) unsigned NOT NULL auto_increment,
  id_produto int(10) unsigned NOT NULL default '0',
  nome char(100) default NULL,
  quantidade mediumint(8) unsigned NOT NULL default '0',
  custo float(4,2) default '0.00',
  qtd_intervalo mediumint(8) unsigned default NULL,
  PRIMARY KEY  (id),
  KEY id_produto (id_produto)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `produto_custo`
#

# --------------------------------------------------------

#
# Estrutura da tabela `produto_percentual_venda`
#

CREATE TABLE produto_percentual_venda (
  id int(10) unsigned NOT NULL auto_increment,
  id_produto int(10) unsigned NOT NULL default '0',
  qtd_atacado mediumint(8) unsigned NOT NULL default '0',
  percentual_venda float(3,1) unsigned NOT NULL default '100.0',
  PRIMARY KEY  (id),
  KEY id_produto (id_produto),
  KEY qtd_atacado (qtd_atacado)
) TYPE=MyISAM;

#
# Extraindo dados da tabela `produto_percentual_venda`
#

# --------------------------------------------------------

#
# Estrutura da tabela `produto_tmp_`
#

CREATE TABLE produto_tmp_ (
  CODIGO varchar(255) NOT NULL default '',
  REFERENCIA varchar(255) NOT NULL default '',
  DESCRICAO varchar(255) NOT NULL default '',
  UN_MED varchar(255) NOT NULL default '',
  OBSERVACAO varchar(255) NOT NULL default '',
  C_VL_MERC varchar(255) NOT NULL default '',
  C_TRANSPORTE varchar(255) NOT NULL default '',
  C_FINANCEIRO varchar(255) NOT NULL default '',
  C_MANUSEIO varchar(255) NOT NULL default '',
  CUSTO_PROD varchar(255) NOT NULL default '',
  CV_ETIQUETA varchar(255) NOT NULL default '',
  CV_MANUSEIO varchar(255) NOT NULL default '',
  CV_MAO_OBRA varchar(255) NOT NULL default '',
  CV_GRAVACAO varchar(255) NOT NULL default '',
  CV_COMISSAO varchar(255) NOT NULL default '',
  CV_COMISSAO_2 varchar(255) NOT NULL default '',
  CV_FOTOLITO varchar(255) NOT NULL default '',
  CV_LASER varchar(255) NOT NULL default '',
  CV_IMPOSTO varchar(255) NOT NULL default '',
  CV_CUSTO varchar(255) NOT NULL default '',
  CV_LUCRO varchar(255) NOT NULL default '',
  VL_VENDA varchar(255) NOT NULL default '',
  PERC_AD_50 varchar(255) NOT NULL default '',
  PERC_AD_100 varchar(255) NOT NULL default '',
  PERC_AD_250 varchar(255) NOT NULL default '',
  PERC_AD_500 varchar(255) NOT NULL default '',
  PERC_AD_1000 varchar(255) NOT NULL default '',
  PERC_AD_2000 varchar(255) NOT NULL default '',
  PERC_AD_5000 varchar(255) NOT NULL default '',
  PERC_AD_10000 varchar(255) NOT NULL default '',
  CTRL_ESTOQUE varchar(255) NOT NULL default '',
  ESTOQUE varchar(255) NOT NULL default '',
  REF_INT varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Extraindo dados da tabela `produto_tmp_`
#


