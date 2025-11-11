CREATE DATABASE IF NOT EXISTS db_saas;
USE db_saas;

CREATE TABLE ESTADO (
    cd_estado INT AUTO_INCREMENT PRIMARY KEY,
    sg_estado CHAR(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE CIDADE (
    cd_cidade INT AUTO_INCREMENT PRIMARY KEY,
    nm_cidade VARCHAR(100) NOT NULL,
    cd_estado INT,
    FOREIGN KEY (cd_estado) REFERENCES ESTADO(cd_estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE RUA (
    cd_rua INT AUTO_INCREMENT PRIMARY KEY,
    nm_rua VARCHAR(100) NOT NULL,
    cd_cidade INT,
    FOREIGN KEY (cd_cidade) REFERENCES CIDADE(cd_cidade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE LOJA (
    cd_cnpj INT PRIMARY KEY,
    ds_senha VARCHAR(100) NOT NULL,
    nm_loja VARCHAR(100) NOT NULL,
    ds_telefone VARCHAR(20),
    ds_email VARCHAR(100),
    ds_categoria VARCHAR(50),
    ds_formato VARCHAR(50),
    cd_estado INT,
    cd_cidade INT,
    cd_rua INT,
    FOREIGN KEY (cd_estado) REFERENCES ESTADO(cd_estado),
    FOREIGN KEY (cd_cidade) REFERENCES CIDADE(cd_cidade),
    FOREIGN KEY (cd_rua) REFERENCES RUA(cd_rua)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE PRODUTO (
    cd_produto INT AUTO_INCREMENT PRIMARY KEY,
    nm_produto VARCHAR(100) NOT NULL,
    ds_produto TEXT,
    vl_produto DECIMAL(10,2) NOT NULL,
    ds_foto VARCHAR(255),
    ds_tipo VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE USUARIO (
    cd_cpf INT PRIMARY KEY,
    nm_usuario VARCHAR(100) NOT NULL,
    ds_email VARCHAR(100),
    ds_senha VARCHAR(100) NOT NULL,
    ds_telefone VARCHAR(20),
    dt_nascimento DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE LOJA_PRODUTO (
    cd_cnpj INT,
    cd_produto INT,
    PRIMARY KEY (cd_cnpj, cd_produto),
    FOREIGN KEY (cd_cnpj) REFERENCES LOJA(cd_cnpj),
    FOREIGN KEY (cd_produto) REFERENCES PRODUTO(cd_produto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE COMPRA (
    cd_cpf INT,
    cd_produto INT,
    dt_compra DATE,
    quantidade INT DEFAULT 1,
    PRIMARY KEY (cd_cpf, cd_produto, dt_compra),
    FOREIGN KEY (cd_cpf) REFERENCES USUARIO(cd_cpf),
    FOREIGN KEY (cd_produto) REFERENCES PRODUTO(cd_produto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
