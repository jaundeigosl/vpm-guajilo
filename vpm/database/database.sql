CREATE TABLE roles
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50) NOT NULL,
    active INT
);

CREATE TABLE users
(
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(255),
    lastname VARCHAR(255),
    email    VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255)        NOT NULL,
    role_id  INT,
    active   INT,
    FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE SET NULL
);

CREATE TABLE region
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50) NOT NULL,
    active INT
);

CREATE TABLE calorific_value
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    day             INT,
    month           INT,
    year            INT,
    calorific_value INT,
    region_id       INT,
    active          INT,
    FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE SET NULL
);

CREATE TABLE prices
(
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    day                 INT            NOT NULL,
    month               INT            NOT NULL,
    year                INT            NOT NULL,
    daily_hsc_price     DECIMAL(10, 4) NOT NULL,
    daily_exchange_rate DECIMAL(10, 4) NOT NULL,
    active              INT
);

CREATE TABLE holidays
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    date       DATE         NOT NULL,
    active     INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products_services
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(50)  NOT NULL, -- Clave
    description VARCHAR(255) NOT NULL, -- Descripción
    line        VARCHAR(100),          -- Línea
    stock       INT       DEFAULT 0,   -- Existencias
    output_unit VARCHAR(50),           -- Unidad salida
    scheme_code VARCHAR(50),           -- Clave esquema
    sat_code    VARCHAR(50),           -- Clave SAT
    unit_code   VARCHAR(50),           -- Clave unidad
    alt_code    VARCHAR(50),           -- Clave alterna
    active      INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE currency_molecule
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50),
    active INT
);

CREATE TABLE currency_service
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50),
    active INT
);

CREATE TABLE molecule_unit
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50),
    active INT
);

CREATE TABLE service_unit
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50),
    active INT
);

CREATE TABLE billing_unit
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(50),
    active INT
);

CREATE TABLE billing_periods
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(50), -- Ej: "Mensual", "Anual"
    start_date    DATE,        -- Opcional si es dinámico
    end_date      DATE,        -- Opcional si es dinámico
    duration_days INT,         -- Ej: 30
    active        INT
);

-- Uso CFDI Gas
CREATE TABLE gas_cfdi_use
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    code   VARCHAR(50),
    name   VARCHAR(250),
    active INT
);

CREATE TABLE service_cfdi_use
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    code   VARCHAR(50),
    name   VARCHAR(250),
    active INT
);

CREATE TABLE productive_sector
(
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(250),
    active INT
);

CREATE TABLE client
(
    id                        INT AUTO_INCREMENT PRIMARY KEY,
    name                      VARCHAR(100),
    alias                     VARCHAR(100),
    number                    VARCHAR(50),
    rfc                       VARCHAR(20),
    email                     VARCHAR(100),
    business_name             VARCHAR(150), -- Razón social
    tax_address               TEXT,         -- Domicilio fiscal
    contact_data              TEXT,
    active                    INT,
    region_id                 INT,
    sector_id                 INT,          -- Sector productivo
    currency_molecule_id      INT,
    currency_service_id       INT,
    molecule_unit_id          INT,
    service_unit_id           INT,
    billing_unit_id           INT,
    billing_period_id         INT,
    gas_cfdi_use_id           INT,
    service_cfdi_use_id       INT,
    gn_molecule_delivery_perm VARCHAR(100),
    gn_service_description    VARCHAR(255),
    hsc_rate                  INT,
    apply_rate_1_025          BOOLEAN,
    fuel_over_hsc             INT,
    gnc_service_rate          INT,
    transport_bf_rate         INT,
    transport_bi_rate         INT,
    created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (region_id) REFERENCES region (id),
    FOREIGN KEY (sector_id) REFERENCES productive_sector (id),
    FOREIGN KEY (currency_molecule_id) REFERENCES currency_molecule (id),
    FOREIGN KEY (currency_service_id) REFERENCES currency_service (id),
    FOREIGN KEY (molecule_unit_id) REFERENCES molecule_unit (id),
    FOREIGN KEY (service_unit_id) REFERENCES service_unit (id),
    FOREIGN KEY (billing_unit_id) REFERENCES billing_unit (id),
    FOREIGN KEY (billing_period_id) REFERENCES billing_periods (id),
    FOREIGN KEY (gas_cfdi_use_id) REFERENCES gas_cfdi_use (id),
    FOREIGN KEY (service_cfdi_use_id) REFERENCES service_cfdi_use (id)
);

CREATE TABLE notas_credito
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id    INT,
    nc DOUBLE,
    concepto      TEXT NOT NULL,
    moneda        ENUM('MXN', 'USD') NOT NULL,
    fecha         DATE NOT NULL,
    subtotal      DECIMAL(15, 2) DEFAULT 0,
    iva           DECIMAL(15, 2) DEFAULT 0,
    total         DECIMAL(15, 2) DEFAULT 0,
    proyecto      VARCHAR(255),
    comentario    TEXT,
    estatus       VARCHAR(100),
    comentarios_2 TEXT,
    created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES client (id) ON DELETE RESTRICT
);

CREATE TABLE facturas
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id    INT,
    factura       VARCHAR(50) NOT NULL,
    concepto      TEXT        NOT NULL,
    fecha         DATE        NOT NULL,
    moneda        ENUM('MXN', 'USD') NOT NULL,
    subtotal      DECIMAL(15, 2) DEFAULT 0,
    iva           DECIMAL(15, 2) DEFAULT 0,
    total         DECIMAL(15, 2) DEFAULT 0,
    abono         DECIMAL(15, 2) DEFAULT 0,
    nc            VARCHAR(255),
    monto_nc      DECIMAL(15, 2) DEFAULT 0,
    saldo_factura DECIMAL(15, 2) DEFAULT 0,
    proyecto      VARCHAR(255),
    estatus       VARCHAR(100),
    fecha_pago    DATE,
    vencimiento   DATE,
    vencidos      INT            DEFAULT 0,
    comentarios   TEXT,
    complemento   VARCHAR(50),
    al_corriente  DECIMAL(15, 2) DEFAULT 0,
    rango_1_15    DECIMAL(15, 2) DEFAULT 0,
    rango_16_30   DECIMAL(15, 2) DEFAULT 0,
    rango_31_45   DECIMAL(15, 2) DEFAULT 0,
    rango_46_60   DECIMAL(15, 2) DEFAULT 0,
    rango_61_90   DECIMAL(15, 2) DEFAULT 0,
    rango_mas_91  DECIMAL(15, 2) DEFAULT 0,
    created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES client (id) ON DELETE RESTRICT
);

CREATE TABLE cuentas_por_cobrar
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id   INT,
    moneda       ENUM('MXN', 'USD') NOT NULL,
    al_corriente DECIMAL(15, 2) DEFAULT 0,
    rango_1_15   DECIMAL(15, 2) DEFAULT 0,
    rango_16_30  DECIMAL(15, 2) DEFAULT 0,
    rango_31_45  DECIMAL(15, 2) DEFAULT 0,
    rango_46_60  DECIMAL(15, 2) DEFAULT 0,
    rango_61_90  DECIMAL(15, 2) DEFAULT 0,
    rango_mas_91 DECIMAL(15, 2) DEFAULT 0,
    saldo_total  DECIMAL(15, 2) DEFAULT 0,
    created_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES client (id) ON DELETE RESTRICT
);

CREATE TABLE vencido_resumen
(
    id                INT AUTO_INCREMENT PRIMARY KEY,
    fecha             DATE NOT NULL,
    tipo_cambio       DECIMAL(10, 4),
    total_vencido_mxn DECIMAL(15, 2),
    total_vencido_usd DECIMAL(15, 2),
    objetivo_mensual  DECIMAL(15, 2),
    recuperado        DECIMAL(15, 2),
    avance            DECIMAL(5, 2),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE vencido_cliente
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    resumen_id     INT,
    cliente_id     INT,
    vencido_mxn    DECIMAL(15, 2) DEFAULT 0,
    vencido_usd    DECIMAL(15, 2) DEFAULT 0,
    total_mxn      DECIMAL(15, 2) DEFAULT 0,
    recuperado_mxn DECIMAL(15, 2) DEFAULT 0,
    recuperado_usd DECIMAL(15, 2) DEFAULT 0,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resumen_id) REFERENCES vencido_resumen (id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES client (id) ON DELETE RESTRICT
);

CREATE TABLE transacciones_canceladas
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    tipo        VARCHAR(50), -- NC, Factura, Refacturacion
    client_id   INT,
    motivo      TEXT,
    comentarios TEXT,
    FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE RESTRICT
);

CREATE TABLE notas_credito_acumuladas
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nc          VARCHAR(50)    NOT NULL,
    monto       DECIMAL(15, 2) NOT NULL,
    moneda      ENUM('MXN', 'USD'),
    responsable VARCHAR(255),
    motivo      TEXT,
    client_id   INT,
    FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE RESTRICT
);

CREATE TABLE pagos_semanales
(
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio         DATE NOT NULL,
    fecha_fin            DATE NOT NULL,
    objetivo_semanal_mxn DECIMAL(15, 2),
    objetivo_mensual_mxn DECIMAL(15, 2),
    avance               DECIMAL(5, 2),
    pendiente            VARCHAR(255),
    tc_prom              DECIMAL(10, 4),
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pagos_dias
(
    id        INT AUTO_INCREMENT PRIMARY KEY,
    semana_id INT,
    dia       VARCHAR(20),
    moneda    ENUM('MXN', 'USD'),
    monto     DECIMAL(15, 2) DEFAULT 0,
    FOREIGN KEY (semana_id) REFERENCES pagos_semanales (id) ON DELETE CASCADE
);

CREATE TABLE perfil_pagos
(
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id            INT,
    dias_credito_molecula INT       DEFAULT 0,
    dias_credito_servicio INT       DEFAULT 0,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES client (id) ON DELETE RESTRICT
);


INSERT INTO facturacion_db.roles (name, active)
VALUES ('admin', 1),
       ('usuario', 1);

INSERT INTO facturacion_db.region (name, active)
VALUES ('agua dulce', 1),
       ('SLP', 1);

INSERT INTO facturacion_db.currency_molecule (name, active)
VALUES ('MXN', 1),
       ('USD', 1);

INSERT INTO facturacion_db.currency_service (name, active)
VALUES ('MXN', 1),
       ('USD', 1);

INSERT INTO facturacion_db.molecule_unit (name, active)
VALUES ('MMBtu', 1),
       ('GJ', 1);

INSERT INTO facturacion_db.service_unit (name, active)
VALUES ('MMBtu', 1),
       ('GJ', 1),
       ('m3', 1);

INSERT INTO facturacion_db.billing_unit (name, active)
VALUES ('MMBtu', 1),
       ('GJ', 1),
       ('m3', 1);

INSERT INTO facturacion_db.gas_cfdi_use (code, name, active)
VALUES ('G01', 'Adquisición de mercancías', 1),
       ('G02', 'Devoluciones, descuentos o bonificaciones', 1),
       ('G03', 'Gastos en general', 1),
       ('I01', 'Construcciones', 1),
       ('I02', 'Mobiliario y equipo de oficina por inversiones', 1),
       ('I03', 'Equipo de transporte', 1),
       ('I04', 'Equipo de cómputo y accesorios', 1),
       ('I05', 'Dados, troqueles, moldes, matrices y herramental', 1),
       ('I06', 'Comunicaciones telefónicas', 1),
       ('I07', 'Comunicaciones satelitales', 1),
       ('I08', 'Otra maquinaria y equipo', 1),
       ('D01', 'Honorarios médicos, dentales y gastos hospitalarios', 1),
       ('D02', 'Gastos médicos por incapacidad o discapacidad', 1),
       ('D03', 'Gastos funerales', 1),
       ('D04', 'Donativos', 1),
       ('D05', 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)', 1),
       ('D06', 'Aportaciones voluntarias al SAR', 1),
       ('D07', 'Primas por seguros de gastos médicos', 1),
       ('D08', 'Gastos de transportación escolar obligatoria', 1),
       ('D09', 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones', 1),
       ('D10', 'Pagos por servicios educativos (colegiaturas)', 1),
       ('CP01', 'Pagos', 1),
       ('CN01', 'Nómina', 1),
       ('S01', 'Sin efectos fiscales', 1);

INSERT INTO facturacion_db.service_cfdi_use (code, name, active)
VALUES ('G01', 'Adquisición de mercancías', 1),
       ('G02', 'Devoluciones, descuentos o bonificaciones', 1),
       ('G03', 'Gastos en general', 1),
       ('I01', 'Construcciones', 1),
       ('I02', 'Mobiliario y equipo de oficina por inversiones', 1),
       ('I03', 'Equipo de transporte', 1),
       ('I04', 'Equipo de cómputo y accesorios', 1),
       ('I05', 'Dados, troqueles, moldes, matrices y herramental', 1),
       ('I06', 'Comunicaciones telefónicas', 1),
       ('I07', 'Comunicaciones satelitales', 1),
       ('I08', 'Otra maquinaria y equipo', 1),
       ('D01', 'Honorarios médicos, dentales y gastos hospitalarios', 1),
       ('D02', 'Gastos médicos por incapacidad o discapacidad', 1),
       ('D03', 'Gastos funerales', 1),
       ('D04', 'Donativos', 1),
       ('D05', 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)', 1),
       ('D06', 'Aportaciones voluntarias al SAR', 1),
       ('D07', 'Primas por seguros de gastos médicos', 1),
       ('D08', 'Gastos de transportación escolar obligatoria', 1),
       ('D09', 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones', 1),
       ('D10', 'Pagos por servicios educativos (colegiaturas)', 1),
       ('CP01', 'Pagos', 1),
       ('CN01', 'Nómina', 1),
       ('S01', 'Sin efectos fiscales', 1);

INSERT INTO facturacion_db.productive_sector (name, active)
VALUES ('Agroindustrial', 1),
       ('Gas Natural Vehicular', 1),
       ('Industria Química', 1),
       ('Invernaderos', 1),
       ('Minería', 1);