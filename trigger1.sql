create or replace TRIGGER BC_BR_BV_RM_EMPLEADOS_DEFAULTS
BEFORE INSERT ON BC_BR_BV_RM_EMPLEADOS
FOR EACH ROW
BEGIN
    -- Generar username basado en nombre y apellido paterno
    :NEW.USERNAME := LOWER(SUBSTR(:NEW.NOMBRE_EMPLEADO, 1, 1) || :NEW.APELLIDO1_EMPLEADO);

    -- Asignar contrase¤a por defecto
    :NEW.PASSWORD := '1234';

    -- Establecer indicador de primera vez en inicio de sesi¢n
    :NEW.PRIMERA_VEZ_INICIO_SESION := 1;
END;