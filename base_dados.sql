create database if not exists db_notas;
use db_notas;

CREATE TABLE IF NOT EXISTS TB_USUARIO (
    ID_USUARIO INT PRIMARY KEY AUTO_INCREMENT,
    NM_USUARIO VARCHAR(50),
    EMAIL VARCHAR(60),
    senha TEXT
);

create table if not exists tb_cliente (
    nr_cnpj char(14) primary key,
    nm_razao_social varchar(30),
    nm_pais varchar(20),
    nm_email varchar(40)
);

create table if not exists tb_nota (
    cd_nota int primary key auto_increment,
    dt_emissao date,
    dt_envio date,
    vl_total decimal(6,2),
    fk_nr_cnpj char(14),

    foreign key (fk_nr_cnpj)
        references tb_cliente(nr_cnpj)
);

-- tabela de produtos
-- (corrigido: faltava a vírgula depois de vl_unitario e havia um ";" no meio da tabela)
create table if not exists tb_produto (
    cd_produto int primary key auto_increment,
    nm_produto varchar(25),
    vl_unitario decimal(5,2),
    nm_imagem varchar(100) null
);

create table if not exists tb_produto_has_tb_nota (
    cd_item int primary key auto_increment,
    fk_cd_produto int,
    fk_cd_nota int,
    qt_produto int,
    vl_total_item decimal(5,2),

    foreign key (fk_cd_produto)
        references tb_produto(cd_produto),

    foreign key (fk_cd_nota)
        references tb_nota(cd_nota)
);

-- ------------------------------------------------------------------
-- Se o banco JÁ existia, rode também estas linhas:
-- ------------------------------------------------------------------
-- ID_USUARIO precisa de AUTO_INCREMENT (o insert_usuario.php não informa o código)
ALTER TABLE TB_USUARIO MODIFY ID_USUARIO INT AUTO_INCREMENT;

-- Só se a tabela tb_produto já existia SEM a coluna da imagem (se der erro "Duplicate column", ignore):
-- ALTER TABLE tb_produto ADD COLUMN nm_imagem VARCHAR(100) NULL;
