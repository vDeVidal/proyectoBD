create or replace FUNCTION BC_BR_BV_RM_CALCULAR_HORAS_EXTRAS (
    P_HORA_INGRESO DATE,
    P_HORA_SALIDA DATE
) RETURN NUMBER IS
    -- Declarar las variables
    V_HORAS_TRABAJADAS NUMBER;
    V_HORAS_EXTRAS NUMBER := 0; -- Inicializamos en 0
    HORAS_ESTANDAR CONSTANT NUMBER := 8; -- Constante de horas estándar

    -- Excepciones personalizadas
    EXCEPTION_HORA_INVALIDA EXCEPTION;
    EXCEPTION_HORA_NULA EXCEPTION;

BEGIN
    -- Validar si las horas de ingreso o salida son nulas
    IF P_HORA_INGRESO IS NULL OR P_HORA_SALIDA IS NULL THEN
        RAISE EXCEPTION_HORA_NULA;
    END IF;

    -- Validar si la hora de ingreso es mayor que la hora de salida
    IF P_HORA_INGRESO > P_HORA_SALIDA THEN
        RAISE EXCEPTION_HORA_INVALIDA;
    END IF;

    -- Calcular la diferencia en horas entre la salida y el ingreso
    V_HORAS_TRABAJADAS := (P_HORA_SALIDA - P_HORA_INGRESO) * 24;

    -- Si las horas trabajadas exceden las horas estándar, calcular las extras
    IF V_HORAS_TRABAJADAS > HORAS_ESTANDAR THEN
        V_HORAS_EXTRAS := V_HORAS_TRABAJADAS - HORAS_ESTANDAR;
    END IF;

    -- Devolver las horas extras
    RETURN V_HORAS_EXTRAS;

EXCEPTION
    -- Manejo de excepciones
    WHEN EXCEPTION_HORA_NULA THEN
        DBMS_OUTPUT.PUT_LINE('Error: Una de las horas (ingreso o salida) es nula.');
        RETURN -1; -- O retornar otro valor para indicar error

    WHEN EXCEPTION_HORA_INVALIDA THEN
        DBMS_OUTPUT.PUT_LINE('Error: La hora de ingreso no puede ser mayor que la hora de salida.');
        RETURN -1; -- O retornar otro valor para indicar error

    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error inesperado: ' || SQLERRM);
        RETURN -1; -- O retornar otro valor para indicar error
END BC_BR_BV_RM_CALCULAR_HORAS_EXTRAS;