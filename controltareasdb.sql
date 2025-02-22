-- Crear la base de datos (opcional, si no existe)
CREATE DATABASE ControlTareas;
GO

USE ControlTareas;
GO

-- Encargados
IF OBJECT_ID('dbo.Encargados', 'U') IS NOT NULL
    DROP TABLE dbo.Encargados;
GO

CREATE TABLE dbo.Encargados (
    EncargadoID INT IDENTITY(1,1) PRIMARY KEY,
    Nombre NVARCHAR(100) NOT NULL
);
GO

-- Grupos
IF OBJECT_ID('dbo.Grupos', 'U') IS NOT NULL
    DROP TABLE dbo.Grupos;
GO

CREATE TABLE dbo.Grupos (
    GrupoID INT IDENTITY(1,1) PRIMARY KEY,
    Nombre NVARCHAR(100) NOT NULL
);
GO

-- Tareas
IF OBJECT_ID('dbo.Tareas', 'U') IS NOT NULL
    DROP TABLE dbo.Tareas;
GO

CREATE TABLE dbo.Tareas (
    TareaID INT IDENTITY(1,1) PRIMARY KEY,
    Detalle NVARCHAR(200) NOT NULL,
    Estado NVARCHAR(20) NOT NULL DEFAULT('Pendiente'),
    FechaFinalizacion DATETIME NULL,
    EncargadoID INT NULL,
    GrupoID INT NULL,
    CONSTRAINT FK_Tareas_Encargados 
        FOREIGN KEY (EncargadoID) REFERENCES dbo.Encargados(EncargadoID)
        ON DELETE SET NULL,
    CONSTRAINT FK_Tareas_Grupos
        FOREIGN KEY (GrupoID) REFERENCES dbo.Grupos(GrupoID)
        ON DELETE SET NULL
);
GO