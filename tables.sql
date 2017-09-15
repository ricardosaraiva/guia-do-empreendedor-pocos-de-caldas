-- tabela para agrupar as empresas do mesmo grupo
CREATE TABLE empresa_grupo (
	id SERIAL PRIMARY KEY,
	id_empresa INT NOT NULL, 
	nome VARCHAR(150) NOT NULL DEFAULT ''
);

-- emails de cadastro da empresa
CREATE TABLE empresa_email (
	id SERIAL PRIMARY KEY,
	id_empresa INT NOT NULL,
	status BOOLEAN NOT NULL DEFAULT true,
	email VARCHAR(100) NOT NULL DEFAULT ''
);

-- telefone da empresa
CREATE TABLE empresa_telefone (
	id SERIAL PRIMARY KEY,
	id_empresa INT(11) NOT NULL ,
	ddd INT(2) NOT NULL DEFAULT 35,
	telefone INT(9),
	status BOOLEAN DEFAULT true
);

-- tabela com as empresas
CREATE TABLE empresa (
	id SERIAL PRIMARY KEY,
	razao VARCHAR(250) NOT NULL,
	fantasia VARCHAR(250) NOT NULL DEFAULT '',
	cnpj CHAR(14) UNIQUE NOT NULL,
	matriz BOOLEAN,
	inscricao_municipal INT NOT NULL,
	situacao BOOLEAN DEFAULT true,
	capital_social NUMERIC NOT NULL DEFAULT 0.0,
	data_abertuda DATE NOT NULL DEFAULT CURRENT_DATE,
	data_cadastro TIMESTAMP NOT NULL DEFAULT NOW(),
	ultima_atualizacao TIMESTAMP NOT NULL DEFAULT NOW()
);

-- zona da cidade (SUL, LESTE, OESTE, NORTE, CENTRO)
CREATE TABLE zona (
	id SERIAL PRIMARY KEY, 
	nome VARCHAR(20) NOT NULL DEFAULT ''
);

-- bairro de cada zona
CREATE TABLE zona_bairro (
	id SERIAL PRIMARY KEY,
	id_zona INT NOT NULL  DEFAULT 0, 
	bairro VARCHAR(20) NOT NULL DEFAULT ''
);

CREATE TABLE empresa_endereco (
	id SERIAL PRIMARY KEY,
	id_empresa INT NOT NULL,
	id_zona INT NOT NULL,
	status BOOLEAN NOT NULL DEFAULT true,
	cep CHAR(8) NOT NULL,
	uf CHAR(2) NOT NULL DEFAULT 'MG',
	cidade VARCHAR(250) NOT NULL,
	bairro VARCHAR(250) NOT NULL,
	logradouro VARCHAR(250) NOT NULL,
	numero INT NOT NULL,
	complemento VARCHAR(250) NOT NULL DEFAULT '',
	latitude FLOAT NOT NULL,
	longitude FLOAT NOT NULL
);

CREATE TABLE empresa_atividade (
	id SERIAL PRIMARY KEY,
	id_atividade INT NOT NULL DEFAULT 0,
	cnae INT NOT NULL,
	princiapal BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE atividade (
	id SERIAL PRIMARY KEY,
	nome VARCHAR(255) NOT NULL DEFAULT ''
);

CREATE TABLE sub_atividade (
	id SERIAL PRIMARY KEY,
	nome VARCHAR(255) NOT NULL DEFAULT ''
);

CREATE TABLE sub_atividade (
	id SERIAL PRIMARY KEY,
	cnae INT NOT NULL DEFAULT 0,
	nome VARCHAR(255) NOT NULL DEFAULT ''
);