create or replace TRIGGER BC_BR_BV_RM_MAYUS_NOMBRE
BEFORE INSERT OR UPDATE ON BC_BR_BV_RM_EMPLEADOS
FOR EACH ROW
BEGIN
    :NEW.NOMBRE_EMPLEADO := INITCAP(LOWER(:NEW.NOMBRE_EMPLEADO));
    :NEW.APELLIDO1_EMPLEADO := INITCAP(LOWER(:NEW.APELLIDO1_EMPLEADO));
    :NEW.APELLIDO2_EMPLEADO := INITCAP(LOWER(:NEW.APELLIDO2_EMPLEADO));
END;