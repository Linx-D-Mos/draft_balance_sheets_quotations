# DOCUMENTO DE ELICITACIÓN DE REQUERIMIENTOS (DER)

# **DOCUMENTO DE ELICITACIÓN DE REQUERIMIENTOS (DER)**

# **Visión y Alcance del Producto (PRD Foundation)**

Proyecto: Sistema de Simulación Financiera, Cotización Dinámica y Control de Márgenes Operativos (Ecosistema V1)

Versión del Alcance: MVP v1.1

Rol del Emisor: Analista de Producto y Arquitecto de Software

Fecha de emisión: Julio 2026

## **1\. Resumen Ejecutivo y Objetivos de Negocio**

El presente sistema está diseñado para reemplazar de forma definitiva el ecosistema descentralizado y propenso a errores humanos basado en hojas de cálculo mutables dentro del sector de la remodelación, pintura y *flooring*.

El software soporta el ciclo de vida completo de un proyecto: desde la parametrización de costos indirectos (Overhead) y la estimación de esfuerzos, hasta el registro de la ejecución real en el campo y la conciliación financiera automática. Esto permite al dueño de la empresa proteger su margen de ganancia neta, simular escenarios de negociación con los clientes y detectar desvíos económicos en tiempo real.

### Metas Métricas a Corto Plazo (MVP)

* Reducción a Cero de errores por mutabilidad accidental en cotizaciones históricas aprobadas.  
* Disminución del 80% en el tiempo de formulación y estructuración de presupuestos y enmiendas de obra.  
* Trazabilidad del 100% de los gastos de mano de obra y materiales aplicados directamente contra la línea base del proyecto.  
* Mitigar al 0% el error humano de vender proyectos por debajo de su costo de equilibrio (*Breakeven*) gracias al cálculo automático del Overhead absorbido.  
* Garantizar visibilidad inmediata del balance financiero de la obra, reportando desvíos mayores al 5% en mano de obra o materiales antes de que afecten la liquidez del negocio.

## **2\. Límites del Mercado y Público Objetivo**

### **S**ectores/Procesos Incluidos (In-Scope)

* Proyectos y obras de remodelación residencial y comercial, pintura de interiores/exteriores y preparación e instalación de acabados de pisos (*flooring*).  
* Control operativo interno y de nivel puramente administrativo (dueño y gestor de operaciones de la empresa).

### Sectores/Procesos Excluidos (Out-of-Scope)

* Gestión de relaciones con clientes (CRM): embudos de venta, captación de leads, notas de seguimiento comercial o agendas de visitas de preventa.  
* Facturación electrónica legal, nómina contable real e integraciones de pasarelas de pago bancarias directas.  
* Control de inventario físico en tiempo real de almacenes o bodegas.

## **3\. Estructura de Límites del MVP (In vs. Out)**

| Áreas / Funcionalidades | Dentro del Alcance (IN \- MVP V1) | Fuera del Alcance (OUT \- Fase 2\) |
| :---- | :---- | :---- |
| Configuración Financiera | Gastos fijos (Overhead) y tarifas horarias por rol. | Contabilidad general, depreciación de activos. |
| Catálogo de Personal | Roster de empleados activos/inactivos (multirol). | Liquidaciones de nómina, control de asistencia biométrico. |
| Estructuración | Creación de Proyectos y Clientes asociados. | CRM de leads, portal de autogestión para clientes. |
| Cotización | Estimador dinámico por pestañas de mano de obra e insumos. | Catálogo de materiales con control de stock de almacén. |
| Protección Contable | Snapshot transaccional de costos al aprobar la cotización. | Firmas digitales integradas de validez legal. |
| Enmiendas de Obra | Máximo de 2 niveles por proyecto (Sustitución Completa). | Árboles de enmiendas ilimitadas y acumulativas. |
| Ejecución de Obra | Bitácora de horas trabajadas, compras reales y anticipos. | Escaneo de facturas por OCR, conciliación bancaria automática. |
| Dashboard | Conciliación gráfica de desvíos "Estimado vs. Real". | Proyecciones predictivas de rentabilidad por IA. |

## **4\. Reglas de Negocio Críticas**

* RN-01 (Línea Base Única por Proyecto): Un proyecto puede tener asociadas múltiples propuestas comerciales en distintos estados, pero únicamente puede existir una cotización activa en estado "Aprobada" en un momento dado.  
* RN-02 (Regla de Sustitución por Enmienda): Al aprobarse formalmente una enmienda de obra, esta se convierte en la nueva y única línea base financiera del proyecto. El registro padre anterior transiciona de forma automática e irreversible al estado "Cerrada por Enmienda", congelando permanentemente su snapshot.  
* RN-03 (Consistencia de Análisis del Dashboard): Las métricas y desvíos financieros mostrados en el Dashboard de Conciliación se calcularán contrastando la ejecución real exclusivamente contra los valores de la cotización activa que cuente con el estado "Aprobada".  
* RN-04 (Integridad del Contenedor de Obra): Todo proyecto registrado en el sistema debe pertenecer obligatoriamente a un único cliente del catálogo.  
* RN-05 (Pertenencia de Propuestas): Toda cotización debe nacer, procesarse y consolidarse asociada estrictamente a un proyecto del sistema.  
* RN-06 (Inmutabilidad Financiera Extrema): Queda estrictamente prohibida la eliminación física (DELETE) de cualquier registro financiero aprobado o ejecutado (cotizaciones aprobadas, bitácoras de horas de personal, compras realizadas o anticipos). Para desactivar registros o corregir errores se aplicarán estados lógicos o anulaciones operativas.  
* RN-07 (Fórmula del Costo por Hora Cargado de Mano de Obra \- $C\_{ch}$):  
  $$C\_{ch} \= \\text{Salario Base} \\times \\left(1 \+ \\frac{\\text{\\% Carga Social}}{100}\\right)$$  
* RN-08 (Fórmula de la Tasa de Overhead por Hora \- $T\_{oh}$):  
  $$T\_{oh} \= \\frac{\\text{Sumatoria de Gastos Fijos Mensuales Activos}}{\\text{Capacidad Estándar de Horas Mensuales de la Empresa}}$$  
* RN-09 (Fórmula del Precio de Venta Sugerido \- $PV$):  
  $$PV \= \\frac{\\text{Costo de Equilibrio (Costo Directo \+ Overhead Absorbido)}}{1 \- \\left(\\frac{\\text{Margen Cotizado}}{100}\\right)}$$

## **5\. Estructura de Épicas e Historias de Usuario (Refactorizadas)**

![Un diagrama jerárquico profesional del 'Mapa de Épicas del MVP'. En la parte superior central, un nodo principal titulado 'MAPA DE ÉPICAS DEL MVP'. De este nodo se desprenden cuatro ramas verticales hacia abajo que conectan con cuatro cuadros o columnas iguales y alineados horizontalmente. Cada cuadro representa una épica: 1. 'Épica 1: Parametrización de Costos (HU-01 a HU-05)', 2. 'Épica 2: Formulación de Cotizaciones (HU-06 a HU-08D)', 3. 'Épica 3: Persistencia e Inmutabilidad (HU-09 a HU-12)', y 4. 'Épica 4: Control Real y Conciliación (HU-13 a HU-16)'. El diseño debe ser limpio, con fuentes legibles y una estructura de mapa mental o de procesos clara.][image1]

# Epicas

## **Mapa de Épicas: MVP Sistema Transaccional de Gestión de Costos y Cotizaciones**

## **ÉPICA 1: PARAMETRIZACIÓN GLOBAL DE COSTOS OPERATIVOS (BACK-OFFICE)**

**Descripción:** Como Administrador de la empresa (Super Admin), quiero contar con un panel unificado de Back-Office en Filament para gestionar los costos fijos (Overhead), definir los roles laborales con sus respectivas cargas fiscales y calcular automáticamente la capacidad laboral mensual de la compañía utilizando un calendario local offline de días festivos de EE. UU. (USA). Esto me permitirá establecer una Tasa de Overhead ($T\_{oh}$) y costos de mano de obra estables, precisos y matemáticamente auditables antes de procesar cualquier simulación o cotización comercial.

**RF-1.1 (Administración de Gastos Fijos):** El sistema debe proveer una interfaz para registrar, actualizar y suspender (desactivar) los gastos fijos mensuales de la empresa (renta, suscripciones, seguros).

**RF-1.2 (Suma Dinámica de Overhead):** El sistema debe recalcular automáticamente el costo total mensual de gastos fijos sumando únicamente los registros que se encuentren marcados con estado activo.

**RF-1.3 (Restricción de Eliminación de Gastos):** El sistema debe denegar la eliminación física de registros de gastos fijos para preservar las auditorías históricas del negocio.

**RF-1.4 (Definición Reactiva de Roles):** El sistema debe proveer un formulario para configurar el catálogo de perfiles de trabajo (pintor, carpintero, preparador) calculando automáticamente su costo cargado por hora ($C\_{ch}$) en pantalla a partir del salario base y el factor de carga social patronal.

**RF-1.5 (Cálculo Offline de Capacidad de Trabajo):** El sistema debe deducir automáticamente y sin necesidad de internet las horas hábiles laborables promedio de la empresa, restando fines de semana y feriados federales de EE. UU. (USA) del año en curso.

**RF-1.6 (Establecimiento de Tasa de Overhead \- $T\_{oh}$):** El sistema debe calcular y guardar automáticamente la Tasa de Overhead por Hora global dividiendo la suma de gastos fijos mensuales activos entre la capacidad promedio mensual de horas laborables.

**RF-1.7 (Panel de Control de Costos Operativos):** El sistema debe consolidar en una pantalla única de solo lectura los indicadores métricos globales de la tasa de overhead, horas de capacidad y total de gastos de la empresa.

**RF-1.8 (Administración Roster de Personal):** El sistema debe permitir la creación de empleados en el sistema limitándose únicamente a capturar su nombre para admitir la naturaleza multirol en el campo de trabajo.

**RF-1.9 (Control de Disponibilidad de Trabajadores):** El sistema debe permitir pausar el estado de un trabajador para excluirlo de asignaciones operativas en futuras cotizaciones o registros reales de horas.

## **Épica 2: MOTOR DE COTIZACIÓN DINÁMICA**

**Descripción:** Como Administrador de la empresa de remodelación y pintura (Super Admin), quiero un motor de cotización centralizado que me permita estructurar propuestas comerciales ágilmente mediante pestañas organizadas, estimando de forma independiente la mano de obra (con horas regulares, extras y número de empleados/cargo) y los materiales directos (pinturas, yeso, herramientas específicas). El sistema debe calcular en tiempo real el costo directo total ($CD$), la porción de Overhead que el proyecto absorberá basándose en el esfuerzo real estimado, y sugerir un precio de venta final libre de pérdidas que, una vez aprobado, congele de forma inmutable la contabilidad de la cotización para protegerla de futuras fluctuaciones de precios o salarios. 

**RF-2.1 (Vinculación Obligatoria a Proyecto):** El sistema debe impedir la creación de cualquier cotización que no esté asociada a un contenedor de proyecto válido.

**RF-2.2 (Creación Rápida de Proyectos en Caliente):** El formulario de creación de cotizaciones debe integrar un botón modal para dar de alta un proyecto y un cliente rápidamente sin salir de la pantalla.

**RF-2.3 (Historial del Contenedor de Proyectos):** El sistema debe listar cronológicamente todas las propuestas comerciales (borradores, enviadas, enmiendas) asociadas a un proyecto dentro de su vista de detalle.

**RF-2.4 (Estructura de Estimación Organizada):** El formulario de cotización debe organizar sus campos de carga de datos en tres secciones claras: Datos Generales, Mano de Obra y Materiales.

**RF-2.5 (Estimación Atómica de Mano de Obra):** El sistema debe proveer un agregador dinámico de personal en el que se puedan simular las horas regulares y extras estimadas de cada rol de trabajo, heredando el costo cargado ($C\_{ch}$) vigente del catálogo global.

**RF-2.6 (Estimación Flexible de Materiales):** El sistema debe permitir la carga libre de materiales e insumos necesarios para la obra capturando concepto de texto, cantidad estimada y costo unitario estimado.

**RF-2.7 (Simulación Financiera en Caliente):** El sistema debe actualizar automáticamente en el pie del formulario de cotización el costo directo, el overhead absorbido, el costo de equilibrio y el precio de venta sugerido utilizando la fórmula de margen de ganancia real sobre precio.

**RF-2.8 (Alerta de Seguridad en Precios):** El sistema debe alertar visualmente al administrador si introduce manualmente un margen que reduzca el precio de venta sugerido por debajo del 10% de utilidad sobre el costo de equilibrio.

**Épica 3: PERSISTENCIA HISTÓRICA, CICLO DE VIDA E INMUTABILIDAD**   
**Descripción:** Garantizar la inmutabilidad de los datos financieros aprobados o enviados y proveer un mecanismo controlado para realizar ajustes de alcance (Enmiendas) y exportación de propuestas profesionales en formato PDF, protegiendo el margen histórico de la empresa ante desviaciones u optimizaciones durante la ejecución.    
**RF-3.1 (Clonación Rápida de Propuestas):** El sistema debe permitir la duplicación de propuestas en estado Borrador, Rechazada o Cancelada a un nuevo borrador, descartando tarifas congeladas y aplicando los costos globales de mano de obra y overhead vigentes el día de hoy.  
**RF-3.2 (Gatillo de Enmiendas):** El sistema debe habilitar la acción de generar enmiendas únicamente sobre cotizaciones que se encuentren en estado "Aprobada".  
**RF-3.3 (Límite Jerárquico de Enmiendas):** El sistema debe verificar que no se creen más de dos niveles de enmiendas vinculadas a un proyecto en la base de datos (Original $\\rightarrow$ Enmienda 1 $\\rightarrow$ Enmienda 2).  
**RF-3.4 (Estructura Dinámica de Borrador de Enmienda):** Al crearse la enmienda en estado borrador, el sistema debe duplicar todo el presupuesto del padre y permitir al administrador añadir, eliminar o reajustar libremente el roster de pintores y materiales directos con tarifas vigentes hoy.  
**RF-3.5 (Sustitución de Línea Base Activa):** Al aprobarse una enmienda, el sistema debe cambiar el estado del padre a "Cerrada por Enmienda" y definir este nuevo registro como el único baseline activo del proyecto para calcular las desviaciones de gastos.  
**RF-3.6 (Pestaña de Historial de Cambios):** El sistema debe renderizar de forma visual un árbol cronológico con los montos y estados de todas las cotizaciones involucradas en el proyecto.  
**RF-3.7 (Exportación Propuesta PDF):** El sistema debe generar un documento PDF limpio y descargable de la cotización que visualice de forma consolidada el personal y materiales, ocultando estrictamente sueldos base y costos cargados internos de la empresa.  
**RF-3.8 (Transición Automática por Descarga):** Al descargarse con éxito el PDF de la cotización, el sistema debe cambiar automáticamente su estado de "Borrador" a "Enviada".

**Épica 4: Balance y Conciliación de Gastos Reales**   
**Descripción:** Proveer al administrador de un motor transaccional para registrar la mano de obra real trabajada, las compras de materiales ejecutadas y los depósitos recibidos por el cliente durante la ejecución de la obra, permitiendo contrastar de forma agregada las desviaciones de costos y evaluar la utilidad neta real obtenida.   
**RF-4.1 (Bitácora de Horas Reales por Colaborador):** El sistema debe proveer una bitácora para registrar jornadas de horas regulares y extras trabajadas de forma individual por empleado asociando el rol desempeñado en ese periodo.  
**RF-4.2 (Cálculo del Costo de Nómina Ejecutado):** El sistema debe calcular en caliente el costo de cada jornada real de trabajo basándose en las horas laboradas, el costo real negociado de la hora y el multiplicador de horas extras de la empresa.  
**RF-4.3 (Bitácora de Compras de Materiales):** El sistema debe proveer una bitácora de tickets de compra de insumos capturando concepto, costo, tienda, método de pago y comprador.  
**RF-4.4 (Control de Excedentes de Compra):** El sistema debe permitir asociar opcionalmente la compra real de un material a un insumo estimado de la cotización aprobada activa. Si se deja libre, el sistema lo catalogará automáticamente como "Gasto Excedente No Presupuestado".  
**RF-4.5 (Registro Contable de Anticipos):** El sistema debe proveer un registro para capturar los abonos de dinero entregados por el cliente a lo largo de la obra.  
**RF-4.6 (Cálculo del Balance de Caja de la Obra):** El sistema debe calcular y mostrar de forma prominente la liquidez operativa restante del proyecto, restando el total de mano de obra y materiales reales pagados al total de abonos recibidos.  
**RF-4.7 (Dashboard Analítico "Estimado vs. Real"):** El sistema debe desplegar un panel interactivo que compare los costos directos, el overhead absorbido y la utilidad neta en dólares de la cotización activa aprobada frente a la suma total de gastos reales.  
**RF-4.8 (Alertas de Pérdida de Margen):** El sistema debe alertar en color rojo si la rentabilidad real calculada cae por debajo del margen pactado originalmente en el contrato por una diferencia mayor al 5%.

# US

## **ÉPICA 1: PARAMETRIZACIÓN GLOBAL DE COSTOS OPERATIVOS (BACK-OFFICE)**

### **HU-01: Control de Gastos Fijos Mensuales (Overhead)**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Registrar, modificar y suspender de manera individual los costos fijos de operación de mi empresa (renta, suscripciones, seguros, telefonía)

**Para:** Que el sistema calcule de forma automática el Overhead Mensual activo de la empresa sin riesgo de alterar la contabilidad de propuestas comerciales ya cerradas.

#### **Criterios de Aceptación Comerciales:**

* **CA-01.1 (Campos del Gasto Fijo):** La interfaz administrativa en Filament proveerá un formulario sencillo para la tabla fixed\_expenses con los siguientes campos:  
  * **Concepto:** Nombre comercial del gasto (ej: "Renta de Bodega", "Seguro de Auto Liability").  
  * **Monto Mensual:** Valor monetario (decimal positivo mayor a cero).  
  * **Estado Activo (Toggle):** Interruptor para encender o apagar el gasto (por defecto activo).  
* **CA-01.2 (Suma Automática de Costos Operativos):** Cada vez que se guarde un cambio o se altere el estado de un gasto, el sistema actualizará dinámicamente el valor del Overhead total sumando únicamente los montos marcados como activos:  
  $$\\text{Overhead Mensual} \= \\sum (\\text{amount}) \\quad \\text{donde } \\text{is\\\_active} \= \\text{true}$$  
* **CA-01.3 (Protección Histórica \- Cero Eliminación en Interfaz):** En alineación con la regla de negocio **RN-06**, el recurso de Filament para gastos fijos (`FixedExpenseResource`) deshabilitará por completo las acciones `DeleteAction` y `BulkDeleteAction`. La baja de un gasto se gestionará únicamente apagando el interruptor `is_active`, garantizando que no existan borrados físicos en la base de datos. 

  ### **HU-02: Definición de Tarifas de Mano de Obra (Labor Roles) con Calculadora de Impuestos**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Configurar un catálogo de puestos de trabajo (pintor, carpintero, preparador) indicando su sueldo base por hora y el porcentaje de carga social/impuestos sobre nómina de la empresa

**Para:** Conocer el costo real cargado por hora de cada rol y utilizarlo como tarifa base en mis estimaciones, evitando cálculos manuales en papel u hojas de cálculo descentralizadas.

#### **Criterios de Aceptación Comerciales:**

* **CA-02.1 (Formulario de Costeo Reactivo):** El formulario de roles en Filament se actualizará en tiempo real en la pantalla a medida que el administrador digite los valores, sin necesidad de recargar la página:  
  * **Nombre del Rol:** Título único del perfil (ej: "Pintor Principal").  
  * **Salario Base por Hora:** Pago nominal por hora (decimal mayor a cero).  
  * **Impuestos sobre Nómina / Carga Social (%):** Porcentaje estimado de cargas federales/estatales de la empresa (ej: 15.00%).  
* **CA-02.2 (Cálculo Inmediato del Costo Cargado \- $C\_{ch}$):** El sistema mostrará de forma visual en un campo bloqueado de solo lectura el costo final real por hora para la empresa aplicando la fórmula:  
  $$C\_{ch} \= \\text{Salario Base} \\times \\left(1 \+ \\frac{\\text{\\% Carga Social}}{100}\\right)$$  
* **CA-02.3 (Persistencia de Tarifa de Cotización):** Al presionar "Guardar", el valor de $C\_{ch}$ se guardará permanentemente en el catálogo para ser consumido de inmediato como la tarifa predeterminada de mano de obra en los nuevos borradores de cotización.

### **HU-03: Calculador de Capacidad de Trabajo y Tasa de Overhead por Hora ($T\_{oh}$)**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Que el sistema calcule de forma automática y offline la capacidad estándar de horas laborables mensuales restando fines de semana y días feriados federales de EE. UU. (USA)

**Para:** Obtener una Tasa de Overhead por Hora ($T\_{oh}$) justa y balanceada, asegurando que cada hora de mano de obra estimada en una cotización absorba la porción exacta de los gastos operativos mensuales de la empresa.

#### **Criterios de Aceptación Comerciales:**

* **CA-03.1 (Algoritmo Calendario Offline):** El sistema calculará anualmente los días laborables restando del calendario de 365 días todos los fines de semana (sábados y domingos) y los días feriados oficiales de EE. UU. (calculados de forma local y offline utilizando la librería Yasumi).  
* **CA-03.2 (Estabilización de Capacidad Mensual):** Para evitar que la tasa de overhead varíe drásticamente en meses de pocos días hábiles (como febrero o diciembre) encareciendo artificialmente los presupuestos, el sistema utilizará una capacidad promedio constante dividiendo las horas hábiles anuales entre los 12 meses:  
  1. $$\\text{Días del Año} \- \\text{Fines de Semana} \- \\text{Feriados USA (Yasumi)} \= \\text{Días Hábiles Anuales}$$  
  2. $$\\text{Horas Hábiles Anuales} \= \\text{Días Hábiles Anuales} \\times 8 \\text{ horas/día}$$  
  3. $$\\text{Capacidad Estándar de Horas Mensuales} \= \\frac{\\text{Horas Hábiles Anuales}}{12}$$  
* **CA-03.3 (Cálculo Automático de Tasa de Overhead \- $T\_{oh}$):** El sistema dividirá los gastos fijos mensuales activos entre la capacidad promedio mensual estándar para generar la tasa operativa base por hora de la empresa:  
  $$T\_{oh} \= \\frac{\\text{Overhead Mensual Sumado}}{\\text{Capacidad Estándar de Horas Mensuales}}$$  
  *Este valor $T\_{oh}$ será persistido en las configuraciones globales (global\_settings) para su uso inmediato en las cotizaciones.*

### **HU-04: Panel de Control de Parámetros Globales**

**Como:** Administrador del Sistema

**Quiero:** Contar con un panel administrativo visual de solo lectura donde pueda auditar de un vistazo la suma de mis gastos fijos activos, la capacidad mensual de horas laborales calculada y la Tasa de Overhead por Hora ($T\_{oh}$) resultante

**Para:** Validar la salud financiera y la tasa de absorción de mis costos operativos antes de procesar cotizaciones comerciales.

#### **Criterios de Aceptación Comerciales:**

* **CA-04.1 (Métricas de Control en Filament):** El panel "Control de Parámetros Globales" mostrará de manera prominente mediante indicadores (*Widgets*) de solo lectura:  
  * **Overhead Mensual Activo:** Sumatoria en tiempo real de los gastos activos de la empresa.  
  * **Horas Laborables Promedio:** La capacidad mensual estándar del año en curso según la **HU-03**.  
  * **Tasa de Overhead Resultante ($T\_{oh}$):** El costo por hora que el negocio debe cobrar de forma indirecta para mantenerse a flote.  
* **CA-04.2 (Actualización Automatizada por Eventos):** El sistema no requerirá acciones manuales para actualizar la tasa. Mediante controladores de eventos de Laravel (*Eloquent Observers*), cualquier cambio en el catálogo de gastos fijos o la transición de año calendario disparará de inmediato el recálculo y la actualización automática del valor en la tabla global\_settings.

### **HU-05: Control y Disponibilidad del Roster de Personal (Catálogo de Empleados)**

*(Alineada al 100% con la base de datos)*

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Registrar y mantener un listado centralizado con los nombres de mi personal disponible

**Para:** Poder llamarlos a trabajar en los proyectos e imputar sus horas reales en el campo, manteniendo la flexibilidad de asignarles cualquier función y tarifa técnica según las necesidades específicas de cada obra.

#### **Criterios de Aceptación Comerciales:**

* **CA-05.1 (Alta de Personal Flexible sin Roles Fijos):** El administrador podrá dar de alta a un nuevo trabajador ingresando únicamente su nombre completo como dato obligatorio. El sistema no exigirá ni asociará un puesto o salario fijo a nivel global, respetando la naturaleza multirol del personal en la empresa.  
* **CA-05.2 (Control de Disponibilidad Operativa):** Cada trabajador contará con un selector visual de estado ("Activo / Inactivo"). Al marcar a un empleado como "Inactivo", este dejará de aparecer inmediatamente en los listados de selección de nuevos proyectos o cotizaciones, evitando asignaciones erróneas de personal que ya no está disponible.  
* **CA-05.3 (Blindaje de Historial Financiero / Cero Eliminación en Interfaz):** Queda estrictamente prohibido el borrado físico de empleados en el sistema. El recurso de Filament `EmployeeResource` tendrá deshabilitadas las opciones `DeleteAction` y `BulkDeleteAction`. La desincorporación de un trabajador se gestionará exclusivamente a través del estado lógico `"Inactivo"`. 

## **ÉPICA 2: MOTOR DE COTIZACIÓN DINÁMICA**

### **HU-06: Estructuración de Propuestas Comerciales Vinculadas a Proyectos**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Que el sistema me obligue a asociar cada propuesta económica (Cotización) a un Proyecto específico del cliente

**Para:** Mantener centralizado el historial de borradores, cotizaciones enviadas, aprobadas y futuras enmiendas de alcance bajo un único contrato maestro, evitando la existencia de cotizaciones "huérfanas" en el sistema.

#### **Criterios de Aceptación Comerciales:**

* **CA-06.1 (Asociación Obligatoria a Proyecto):** El formulario de creación de cotizaciones exigirá obligatoriamente seleccionar un Proyecto activo (project\_id). No se permitirá guardar ninguna cotización en el aire.  
* **CA-06.2 (Acceso Directo y Creación de Proyectos en Caliente):** Si el proyecto es nuevo, la interfaz en Filament proveerá un botón de acceso rápido dentro del selector que abrirá un modal para crear el Proyecto y asociarle el Cliente sin abandonar la pantalla de cotización ni perder la información digitada.  
* **CA-06.3 (Trazabilidad en el Contenedor de Proyectos):** Al visualizar la pantalla de un Proyecto en Filament, el sistema renderizará una tabla con el listado cronológico de todas las cotizaciones vinculadas, mostrando su código, fecha de creación, costo directo total y su estado actual.

### **HU-07A: Configuración Comercial y Margen de la Propuesta**

**Como:** Administrador del Negocio

**Quiero:** Inicializar una propuesta económica seleccionando el proyecto asociado, el cliente y definiendo el margen de ganancia comercial deseado

**Para:** Establecer la base del beneficio económico que pretendo percibir en la obra antes de proceder a la carga de costos directos.

#### **Criterios de Aceptación Comerciales:**

* **CA-07A.1 (Campos Base de Inicialización):** El sistema presentará la primera pestaña del formulario de Filament ("General") con los siguientes controles obligatorios: Selector de Cliente, Selector de Proyecto (project\_id) y campo numérico de Margen de Ganancia (margin\_applied).  
* **CA-07A.2 (Precarga Automatizada de Parámetros Globales):** Al abrir el formulario de una nueva cotización, el campo margin\_applied se inicializará automáticamente consumiendo el valor guardado en global\_settings.default\_profit\_margin (registro ID 1), pero se mantendrá 100% editable.

### **HU-07B: Calculador Reactivo de Fecha de Fin (Modo Duración)**

**Como:** Estimador del Negocio

**Quiero:** Que el sistema calcule dinámicamente la fecha estimada de finalización a partir de la fecha de inicio y una duración especificada, controlando la inclusión o exclusión de días no hábiles

**Para:** Ofrecer un plazo de entrega realista al cliente basándome en los días del calendario en que operará el equipo.

#### **Criterios de Aceptación Comerciales:**

* **CA-07B.1 (Cálculo Dinámico de Fecha de Fin):** Al ingresar la *Fecha de Inicio* y digitar una *Duración Estimada* (en días hábiles o semanas), Filament calculará automáticamente el campo end\_date bajo las siguientes reglas reactivas:  
  * **Si work\_weekends está APAGADO (False):** El sistema sumará los días de duración saltándose todos los sábados, domingos y días feriados federales de EE. UU. (calculados localmente con Yasumi), empujando la fecha de fin hacia adelante.  
  * **Si work\_weekends está ENCENDIDO (True):** El sistema sumará los días de duración de corrido (días naturales).

### **HU-07C: Calculador de Días Hábiles Disponibles (Modo Rango Fijo)**

**Como:** Estimador del Negocio

**Quiero:** Que el sistema deduzca la cantidad de días hábiles reales de ejecución si el cliente impone un rango de fechas de inicio y fin inamovibles

**Para:** Alertar visualmente si el margen de tiempo es muy ajustado para el esfuerzo estimado.

#### **Criterios de Aceptación Comerciales:**

* **CA-07C.1 (Cálculo Dinámico de Capacidad de Días):** Si el usuario selecciona manualmente tanto la *Fecha de Inicio* como la *Fecha de Fin*, el sistema mostrará de inmediato un indicador visual de solo lectura con el total de **"Días Hábiles Disponibles para Ejecución"**:  
  * **Si work\_weekends está APAGADO (False):** Mostrará el conteo neto restando fines de semana y feriados USA (Yasumi).  
  * **Si work\_weekends está ENCENDIDO (True):** Mostrará el conteo total de días naturales del rango.  
  * 

### **HU-08: Estimador Reactivo de Mano de Obra (Esfuerzo Atómico)**

**Como:** Administrador del Negocio

**Quiero:** Estimar el esfuerzo de mano de obra agregando individualmente las posiciones de los pintores o instaladores, asignando sus roles y proyectando sus horas de trabajo

**Para:** Conocer instantáneamente el costo directo de nómina de la obra basado en las tarifas por hora vigentes de mi catálogo de roles.

#### **Criterios de Aceptación:**

* **CA-08.1:** El sistema proveerá la pestaña "Mano de Obra" estructurada como un sumador dinámico (Repeater) donde cada fila representa una asignación individual.  
* **CA-08.2:** Por cada fila, el usuario seleccionará el Rol de Trabajo (labor\_role\_id), pudiendo opcionalmente seleccionar un empleado real (employee\_id) o escribir un marcador de posición de texto libre (worker\_name\_placeholder, ej: "Pintor Auxiliar 1").  
* **CA-08.3:** Al ingresar las horas normales y extras estimadas en la fila, el sistema calculará en tiempo real el subtotal del costo de ese colaborador usando la tarifa por hora cargada de la empresa ($C\_{ch}$) y el multiplicador de hora extra vigente.

### **HU-09: Estimador de Materiales de Compra Directa (Insumos Libres)**

**Como:** Administrador del Negocio

**Quiero:** Listar libremente los materiales e insumos necesarios para la obra, especificando su descripción, cantidad y costo unitario estimado

**Para:** Computar el costo directo de materiales de la propuesta de forma flexible y adaptada a las necesidades específicas de la obra, sin depender de un catálogo rígido preexistente.

#### **Criterios de Aceptación:**

* **CA-09.1:** El sistema proveerá la pestaña "Materiales" estructurada como un sumador dinámico (Repeater) que permitirá agregar insumos mediante campos de texto y numéricos libres.  
* **CA-09.2:** Cada fila requerirá obligatoriamente: Concepto (ej. "Benjamin Moore 5 Galones", "Drywall Screws 1 Box"), Cantidad Estimada y Costo Unitario Estimado.  
* **CA-09.3:** El sistema calculará automáticamente en la pantalla el subtotal de cada fila multiplicando la cantidad estimada por el costo unitario estimado.

### **HU-10: Cálculo en Caliente del Costo Directo ($CD$)**

**Como:** Estimador de Proyectos

**Quiero:** Que el sistema sume automáticamente el subtotal acumulado de las pestañas de Mano de Obra y Materiales en el pie del formulario

**Para:** Conocer la base del costo directo de la obra instantáneamente a medida que añado filas a los repeaters.

#### **Criterios de Aceptación Comerciales:**

* **CA-10.1 (Suma Reactiva de Mano de Obra):** Al agregar, eliminar o modificar horas de un rol en el agregador de mano de obra, Filament recalculará el subtotal de nómina en pantalla sin recargar la página.  
* **CA-10.2 (Suma Reactiva de Materiales):** Al ingresar costos o cantidades de materiales directos, el subtotal se actualizará instantáneamente.  
* **CA-10.3 (Consolidación de Costo Directo):** El campo del Costo Directo ($CD$) mostrará la suma matemática en vivo:  
  $$CD \= \\sum(\\text{Subtotales de Mano de Obra}) \+ \\sum(\\text{Subtotales de Materiales})$$

### **HU-11: Cálculo en Caliente del Overhead Absorbido ($OH$)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Que el sistema multiplique en vivo las horas de esfuerzo estimadas por la Tasa de Overhead ($T\_{oh}$) activa

**Para:** Visualizar con precisión qué porción de los costos fijos de mi oficina está absorbiendo el proyecto en el campo.

#### **Criterios de Aceptación Comerciales:**

* **CA-11.1 (Detección de Horas de Esfuerzo):** El sistema sumará en tiempo real todas las horas (regulares y extras) de los trabajadores asignados en la pestaña de mano de obra.  
* **CA-11.2 (Cálculo del Overhead Absorbido):** Multiplicará el total de horas obtenidas en el CA-11.1 por la $T\_{oh}$ guardada en global\_settings:  
  $$OH \= \\text{Horas Totales Estimadas} \\times T\_{oh}$$  
* **CA-11.3 (Actualización Dinámica):** El valor de Overhead absorbido se actualizará en el formulario en vivo cada vez que se modifique una sola hora de esfuerzo en el roster.

### **HU-12: Cálculo del Costo de Equilibrio ($CE$) y Precio de Venta Sugerido ($PV$)**

**Como:** Administrador del Negocio

**Quiero:** Que el sistema combine los costos directos e indirectos para mostrarme el precio sugerido de venta en base al margen comercial deseado

**Para:** Presentar propuestas comerciales que garanticen la rentabilidad de la empresa y cubran el punto de equilibrio (Breakeven).

#### **Criterios de Aceptación Comerciales:**

* **CA-12.1 (Costo de Equilibrio):** El sistema sumará el Costo Directo ($CD$) y el Overhead Absorbido ($OH$) para pintar de solo lectura el Costo de Equilibrio ($CE$):  
  $$CE \= CD \+ OH$$  
* **CA-12.2 (Precio de Venta Sugerido):** Aplicará la fórmula de margen sobre precio de venta, basándose en el porcentaje introducido en el input margin\_applied:  
  $$PV \= \\frac{CE}{1 \- \\left(\\frac{\\text{margin\\\_applied}}{100}\\right)}$$  
* **CA-12.3 (Reactividad del Margen):** Si el usuario altera el margen deseado (ej: de 20% a 35%), el precio sugerido se recalculará instantáneamente en pantalla.

### **HU-13: Alerta de Seguridad de Margen de Ganancia Mínimo**

**Como:** Dueño de la Empresa

**Quiero:** Que el sistema resalte visualmente en color rojo el precio de venta si el margen aplicado cae por debajo del 10% sobre el Costo de Equilibrio

**Para:** Evitar que mis estimadores o yo mismo vendamos proyectos por debajo del costo real operativo por un error de digitación.

#### **Criterios de Aceptación Comerciales:**

* **CA-13.1 (Gatillo de Alerta):** El sistema evaluará el precio final resultante frente al Costo de Equilibrio ($CE$). Si el margen de utilidad neta real proyectado es inferior al 10%, el campo de precio de venta se sombreará en rojo.  
* **CA-13.2 (Mensaje de Advertencia):** Se desplegará un texto dinámico de advertencia debajo del input que dirá: *"¡Alerta Financiera\! El precio configurado no cubre el margen mínimo de seguridad de la empresa (10%)."*

## **ÉPICA 3: PERSISTENCIA HISTÓRICA, CICLO DE VIDA E INMUTABILIDAD**

### **HU-14: Ciclo de Vida y Transición de Estados de Propuestas**

**Como:** Administrador del Negocio

**Quiero:** Controlar el flujo de transiciones de mis propuestas comerciales (Borrador, Enviada, Aprobada, Cancelada)

**Para:** Garantizar que las cotizaciones sigan un proceso ordenado y que las reglas de negocio de inmutabilidad se activen en los momentos correctos.

#### **Criterios de Aceptación:**

* **CA-14.1:** El sistema controlará estrictamente el flujo de estados: una propuesta nace en "Borrador", puede pasar a "Enviada", y de "Borrador" o "Enviada" puede transicionar a "Aprobada" o "Cancelada".  
* **CA-14.2:** No se permitirá revertir una propuesta a "Borrador" una vez que ha sido aprobada o cerrada por enmienda, protegiendo el flujo lógico del contrato.

### **HU-15: Transición Controlada al Estado "Aprobada"**

**Como:** Administrador de la Plataforma

**Quiero:** Que el sistema valide las reglas de negocio de consistencia antes de permitir cambiar el estado de una propuesta a "Aprobada"

**Para:** Evitar inconsistencies de estados, asegurando que un proyecto solo tenga un único baseline de comparación.

#### **Criterios de Aceptación Comerciales:**

* **CA-15.1 (Validación de Línea Base Única \- RN-01):** Al intentar aprobar una cotización, el sistema buscará si el proyecto ya tiene otra cotización aprobada. Si existe una activa, denegará la acción e indicará al usuario que debe archivarla o enmendarla primero.  
* **CA-15.2 (Flujo de Enmienda Directo \- RN-02):** Si la cotización que se aprueba es una Enmienda, el sistema transicionará de forma automática el registro padre anterior al estado "Cerrada por Enmienda".  
* **CA-15.3 (Activación de Estado de Proyecto Automático):** Al aprobarse la primera cotización de un proyecto (pasando de `draft` o `sent` a `approved`), el sistema disparará un evento para actualizar automáticamente el estado del proyecto padre (`projects.project_status_id`) al valor **"En Ejecución"** (`in_progress`), eliminando la necesidad de que el usuario lo modifique de forma manual en un panel secundario. 

### **HU-16: Persistencia Física del Snapshot Financiero (Congelamiento Contable)**

**Como:** Administrador Financiero

**Quiero:** Que al momento de aprobar la propuesta, el sistema guarde una copia inmutable de todas las tarifas globales utilizadas en las tablas de la transacción

**Para:** Proteger el presupuesto histórico del proyecto de futuros incrementos de precios de materiales o aumentos en la nómina general de la compañía.

#### **Criterios de Aceptación Comerciales:**

* **CA-16.1 (Transaccionalidad Atómica):** El proceso de congelamiento se ejecutará bajo una transacción de base de datos (DB::transaction). Si un solo guardado falla, se aplicará rollback de todo el proceso.  
* **CA-16.2 (Volcado de Tarifas):** Se copiará el valor de la $T\_{oh}$ y el $C\_{ch}$ actual a columnas dedicadas en la base de datos:  
  * quotes.overhead\_rate\_applied $\\leftarrow$ Tasa global del día de la aprobación.  
  * quote\_labor\_assignments.hourly\_rate\_at\_estimation $\\leftarrow$ Tarifa del rol del día de la aprobación.  
  * quote\_material\_items.estimated\_unit\_price $\\leftarrow$ Costo unitario estimado del día de la aprobación.

### **HU-17: Bloqueo Hermético de Interfaz (Modo Solo Lectura)**

**Como:** Administrador de la Plataforma

**Quiero:** Que el formulario completo de la cotización se desactive visualmente una vez que la propuesta comercial pase a estado "Enviada" o "Aprobada"

**Para:** Evitar que cualquier usuario pueda alterar de forma accidental los números que ya fueron enviados al cliente o aprobados para ejecución.

#### **Criterios de Aceptación Comerciales:**

* **CA-17.1 (Deshabilitación del Formulario):** En Filament, si el estado de la cotización es sent, approved o closed\_by\_amendment, todos los campos de texto, selectores y repeaters pasarán a modo .disabled().  
* **CA-17.2 (Ocultamiento de Acciones de Edición):** Los botones de "Agregar material", "Agregar pintor" y "Guardar" se removerán por completo de la vista de detalle para impedir llamadas innecesarias al backend.

### **HU-18: Bloqueo de Interfaz y Modo Solo Lectura de Propuestas**

**Como:** Administrador del Negocio

**Quiero:** Que el formulario de cotización se bloquee por completo en la pantalla una vez que la propuesta ha sido aprobada o enviada

**Para:** Impedir modificaciones accidentales en los datos del presupuesto por parte de los usuarios del sistema.

#### **Criterios de Aceptación:**

* **CA-18.1:** Al visualizar una cotización en estado "Enviada", "Aprobada" o "Cerrada por Enmienda", el sistema deshabilitará todos los inputs de texto, selectores, botones de agregar y repeaters del formulario.  
* **CA-18.2:** El formulario mostrará una insignia visual clara en la parte superior que indique: "Solo Lectura \- Propuesta \[Estado\]".

### **HU-19: Clonación de Cotizaciones para Contrapropuestas**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Duplicar de forma rápida una cotización existente que fue cancelada, rechazada o que aún está en borrador

**Para:** Generar una nueva propuesta modificable sin tener que digitar todo el presupuesto desde cero, recalculando de inmediato los costos de mano de obra y materiales con las tarifas y Overhead vigentes al día de hoy.

#### **Criterios de Aceptación Comerciales:**

* **CA-19.1 (Gatillo de Clonación):** La opción "Clonar Cotización" estará disponible visualmente en la barra de acciones de Filament para cualquier cotización que no esté en estado "Aprobada" o "Enviada" (es decir, permitida en Borrador, Rechazada o Cancelada).  
* **CA-19.2 (Estructuración del Clon y Reseteo Financiero):** El sistema creará una copia idéntica de la cotización original bajo las siguientes reglas:  
  * Hereda el cliente, el proyecto (project\_id), el rango de fechas, los roles asignados, cantidades de materiales y configuraciones de días festivos.  
  * El estado de la nueva cotización se establece obligatoriamente en "Borrador".  
  * Se descartan por completo los snapshots financieros de la cotización origen. El nuevo borrador aplica en caliente el costo cargado por hora ($C\_{ch}$) de los roles y la Tasa de Overhead ($T\_{oh}$) activos hoy en la configuración global.  
* **CA-19.3 (Redirección Administrativa):** Tras procesar la copia de forma segura en la base de datos, el sistema redirigirá automáticamente al administrador al formulario de edición de la nueva propuesta para que realice los ajustes solicitados por el cliente.

### **HU-20: Iniciación de Enmienda y Límite de Jerarquía**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Iniciar un flujo formal de enmienda sobre una cotización aprobada y validar que el proyecto no exceda el límite administrativo de modificaciones

**Para:** Controlar los cambios de alcance de las obras y restringir las modificaciones a un historial máximo de dos niveles para mantener la estabilidad operativa del proyecto.

#### **Criterios de Aceptación:**

* **CA-20.1:** En la vista de detalle de una cotización "Aprobada", se habilitará la acción "Generar Enmienda".  
* **CA-20.2**: (Límite Jerárquico Directo por Nivel de Enmienda): El sistema utilizará la columna amendment\_level de la cotización para validar la profundidad del historial sin realizar consultas recursivas.Si la cotización padre tiene un amendment\_level mayor o igual a 2, el botón "Generar Enmienda" estará completamente deshabilitado en Filament.Al guardar la enmienda en borrador, el sistema asignará de manera automática al nuevo registro:$$\\text{Nuevo } amendment\\\_level \= \\text{amendment\\\_level del Padre} \+ 1$$  
* **CA-20.3:** Al procesar con éxito la acción, el sistema cambiará de forma automática el estado de la cotización padre a "Cerrada por Enmienda" (closed\_by\_amendment), congelando permanentemente su snapshot original.

### **HU-21: Ajuste Flexible de Personal y Materiales en Enmienda Borrador**

**Como:** Administrador del Negocio

**Quiero:** Que la nueva enmienda nazca como una copia del presupuesto anterior en estado de borrador, permitiéndome añadir, eliminar o modificar libremente a los pintores y materiales

**Para:** Rediseñar la estructura de costos y alcance de la obra basándome en las nuevas necesidades del cliente, recalculando los costos con las tarifas globales vigentes al día de hoy.

#### **Criterios de Aceptación:**

* **CA-21.1:** La nueva enmienda se creará en estado "Borrador", vinculada a la cotización padre mediante parent\_quote\_id y al proyecto maestro mediante project\_id.  
* **CA-21.2:** El sistema duplicará todas las filas de mano de obra y materiales del presupuesto padre en el nuevo formulario editable.  
* **CA-21.3:** El administrador podrá interactuar libremente con los repeaters para: agregar nuevos roles de pintores, eliminar asignaciones anteriores que ya no participarán en la obra, reajustar horas normales/extras y modificar conceptos de materiales. Las tarifas de los roles se actualizarán automáticamente con los costos cargados ($C\_{ch}$) vigentes hoy en la empresa.

### **HU-22: Activación de Nueva Línea Base (Aprobación de Enmienda)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Que al aprobar la nueva enmienda, esta sustituya por completo la línea base presupuestaria del proyecto

**Para:** Que el módulo de control real contrastre los gastos de campo exclusivamente contra esta nueva foto aprobada activa, simplificando el análisis de desvíos financieros.

#### **Criterios de Aceptación:**

* **CA-22.1:** Al pasar la cotización de enmienda al estado "Aprobada", el sistema la marcará como la única línea base activa del proyecto.  
* **CA-22.2:** El sistema ejecutará el snapshot financiero de la enmienda (**HU-16**), congelando físicamente sus tarifas y totales independientes en la base de datos.  
* **CA-22.3:** A partir de este momento, cualquier consulta del Dashboard de balance financiero ignorará los presupuestos anteriores y calculará las desviaciones de mano de obra y materiales usando únicamente los datos de esta enmienda aprobada.

### **HU-23: Visualización del Historial de Cambios (Trazabilidad)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Visualizar una pestaña con el historial cronológico y jerárquico de todas las modificaciones que ha sufrido el presupuesto del proyecto

**Para:** Auditar la evolución del precio del contrato con el cliente, viendo con claridad los montos de cada versión y quién la aprobó.

#### **Criterios de Aceptación:**

* **CA-23.1:** En la vista de detalle del Proyecto o de la Cotización activa, Filament renderizará una pestaña llamada "Historial de Cambios".  
* **CA-23.2:** Esta pestaña mostrará una tabla ordenada cronológicamente con la cadena de enmiendas vinculadas (Original, Enmienda 1, Enmienda 2), detallando por fila: Código de cotización, Fecha de registro, Estado, Horas Totales estimadas, Costo Directo y el Precio Final de Venta cobrado al cliente.

### **HU-24A: Generación de Propuesta Comercial Oculta (PDF Cliente)**

* ### **Como:** Administrador del Negocio

* ### **Quiero:** Abrir un modal en Filament que me permita descargar un PDF limpio de mi cotización orientado al cliente

* ### **Para:** Presentar un presupuesto profesional que proteja los costos internos y salarios de la empresa de miradas ajenas.

#### **Criterios de Aceptación Comerciales:**

* ### **CA-24A.1 (Gatillo Interactivo de Descarga):** El botón "Generar PDF" en Filament abrirá un modal de configuración. Por defecto, estará seleccionada la opción *"Propuesta Comercial (Cliente)"*. Al presionar "Descargar", se procesará el archivo sin alterar el estado de edición de la cotización en la plataforma.

* ### **CA-24A.2 (Maquetación y Censura Financiera de Cliente):** El archivo PDF generado mostrará el diseño ejecutivo de la obra incluyendo: Datos generales, fechas estimadas, desglose de roles con horas totales y conceptos de materiales con sus cantidades.

* ### **CA-24A.3 (Blindaje de Datos Internos \- RN-06):** El reporte **ocultará estrictamente** los salarios base de los trabajadores, los costos cargados reales ($C\_{ch}$), los precios unitarios estimados de los materiales y las tasas o costos de overhead absorbido. El único valor monetario visible será el **Precio Final de Venta** destacado en grande.

### **HU-24B: Variaciones del PDF para Campo y Administración (Filtros Dinámicos)**

* ### **Como:** Gestor de Operaciones y Administrador Financiero

* ### **Quiero:** Seleccionar en el modal de descarga si el PDF va dirigido al equipo de pintores en el campo o a la auditoría interna de la oficina

* ### **Para:** Disponer de reportes técnicos operativos o análisis contables consolidados según la necesidad del momento.

#### **Criterios de Aceptación Comerciales:**

* ### **CA-24B.1 (Inclusión de Perfiles en el Selector):** El selector del modal de la HU-24A incluirá dos nuevas opciones: *"Orden de Trabajo / Lista de Campo"* y *"Reporte Interno Consolidado"*.

* ### **CA-24B.2 (Filtro Técnico para Campo \- Orden de Trabajo):** Si se selecciona la opción de campo, el PDF se generará mostrando únicamente la lista de materiales (conceptos y cantidades netas para compra o retiro de bodega) y las horas de esfuerzo estimadas por rol. Este documento **censurará el 100% de los datos monetarios** (sin precios unitarios, subtotales ni precio final de venta).

* ### **CA-24B.3 (Transparencia Total para Administración):** Si se selecciona la opción interna, el PDF se descargará como una copia idéntica y sin restricciones de la simulación financiera en pantalla, detallando: costos directos ($CD$), overhead absorbido ($OH$), costo de equilibrio ($CE$), margen aplicado y precio final de venta.

### **HU-24C: Acción de Emisión Formal y Bloqueo de Edición (Cierre de Ciclo)**

### **Como:** Administrador del Negocio

### **Quiero:** Contar con una acción independiente en la interfaz para marcar formalmente la propuesta como emitida al cliente

* ### **Para:** Congelar el presupuesto de forma voluntaria, evitando que modificaciones accidentales en el borrador alteren las condiciones enviadas al cliente.

#### **Criterios de Aceptación Comerciales:**

* ### **CA-24C.1 (Gatillo de Cierre de Edición):** La barra de acciones de Filament mostrará un botón explícito llamado *"Emitir y Bloquear Propuesta"*. Esta acción requerirá una confirmación secundaria del usuario antes de ejecutarse.

* ### **CA-24C.2 (Transición de Estado Segura):** Al confirmar, el sistema traicionará el estado de la cotización de Borrador a Enviada en la base de datos de manera transaccional.

* ### **CA-24C.3 (Bloqueo Hermético de Interfaz):** En el instante en que el estado pase a Enviada, Filament activará el modo solo lectura de forma reactiva, deshabilitando todos los campos de texto, selectores y repeaters de mano de obra y materiales, removiendo además los botones de guardado.


## **ÉPICA 4: BALANCE Y CONCILIACIÓN DE GASTOS REALES**

### **HU-25A: Registro Atómico de Jornada de Trabajo**

**Como:** Administrador del Negocio

**Quiero:** Registrar de forma diaria o semanal las horas reales trabajadas por mi personal en un proyecto activo

**Para:** Imputar de forma atómica el esfuerzo de mano de obra en el campo.

#### **Criterios de Aceptación Comerciales:**

* **CA-25A.1 (Registro de Jornada Atómico):** En la pantalla de gestión del proyecto en ejecución, Filament proveerá un formulario de carga rápida para registrar jornadas individuales en la tabla `project_labor_logs`:  
  * **Trabajador:** Selector que consume la lista de empleados activos (*employees*).  
  * **Rol Desempeñado:** Selector para definir qué función realizó en esta jornada (*labor\_roles*).  
  * **Horas Regulares Reales:** Horas estándar trabajadas (mayor o igual a cero).  
  * **Horas Extras Reales:** Horas adicionales trabajadas (mayor o igual a cero).  
  * **Tarifa Real por Hora Pagada:** Campo monetario, precargado por defecto con el costo cargado ($C\_{ch}$) del rol seleccionado para ahorrar digitación, pero **100% editable**.

### **HU-25B: Cálculo de Nómina Ejecutada e Inmutabilidad del Multiplicador**

**Como:** Administrador Financiero del Negocio

**Quiero:** Que el sistema calcule automáticamente el costo total real de cada jornada utilizando un snapshot del multiplicador de horas extras del día del registro

**Para:** Evitar que los balances históricos acumulados de la obra se alteren retroactivamente si cambio el multiplicador de la empresa en el futuro.

#### **Criterios de Aceptación Comerciales:**

* **CA-25B.1 (Fórmula del Subtotal Real de Nómina):** Al guardar la jornada, el sistema multiplicará las horas reales por la tarifa real y por el multiplicador de horas extras ($M\_{he}$) vigente en ese instante de tiempo en global\_settings.overtime\_multiplier.  
* **CA-25B.2 (Volcado de Snapshot del Multiplicador):** El multiplicador utilizado se guardará físicamente en la columna project\_labor\_logs.overtime\_multiplier\_applied. El valor calculado se guardará de forma física y definitiva en la columna actual\_subtotal aplicando la fórmula:  
  $$\\text{actual\\\_subtotal} \= \\left( \\text{actual\\\_hours\\\_regular} \\times \\text{hourly\\\_rate\\\_actual} \\right) \+ \\left( \\text{actual\\\_hours\\\_extra} \\times \\text{hourly\\\_rate\\\_actual} \\times \\text{overtime\\\_multiplier\\\_applied} \\right)$$  
* **CA-25B.3 (Blindaje Histórico):** Si en el futuro un administrador modifica el multiplicador de horas extras en global\_settings, el sistema **no recalculará** las filas existentes en project\_labor\_logs, garantizando que el balance real acumulado de la obra no sufra alteraciones retroactivas.

### **HU-25C: Anulación Controlada de Logs de Trabajo (Audit Trail)**

**Como:** Administrador y Dueño de la Empresa

**Quiero:** Anular de forma lógica un registro incorrecto de horas reales de trabajo, registrando el motivo y el usuario responsable

**Para:** Corregir errores humanos de digitación sin violar la regla de inmutabilidad extrema (cero eliminación física).

#### **Criterios de Aceptación Comerciales:**

* **CA-25C.1 (Cero Borrados de Jornadas Reales):** En cumplimiento de la **RN-06**, el recurso de Filament `ProjectLaborLogResource` tendrá deshabilitada la eliminación física de registros. Las correcciones de horas se realizarán mediante un botón de acción llamado *"Anular Log"*.  
* **CA-25C.2 (Formulario de Anulación y Auditoría):** Al presionar "Anular Log", el sistema abrirá un modal interactivo que requerirá obligatoriamente ingresar un "Motivo de la Anulación". Tras la confirmación, el sistema ejecutará de manera transaccional:  
  * Cambiar `is_annulled` a *true*.  
  * Guardar `annulled_by_user_id` $\\leftarrow$ ID del usuario autenticado.  
  * Guardar `annulled_at` $\\leftarrow$ Timestamp actual.  
  * Guardar `annulment_reason` $\\leftarrow$ Motivo de la anulación ingresado por el usuario.  
* **CA-25C.3 (Exclusión de Agregados Financieros):** El sistema excluirá de forma inmediata el subtotal de este log (*actual\_subtotal*) de cualquier suma real en el Dashboard de Conciliación y en el Balance de Caja.

### **HU-26A: Registro de Compras Reales de Materiales (Control de Desviaciones)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Registrar cada ticket o factura de compra de materiales realizada en las tiendas, indicando el costo, la tienda, el pagador y el método de pago

**Para:** Mantener una bitácora detallada de los gastos directos de insumos, cruzando opcionalmente cada compra con los conceptos que estimé en la propuesta.

#### **Criterios de Aceptación Comerciales:**

* **CA-26A.1 (Formulario de Ingesta de Facturas):** El sistema proveerá una sección interactiva para registrar compras en la tabla `project_material_purchases` con los siguientes campos:  
  * **Insumo Estimado Vinculado (Opcional):** Un selector que listará únicamente los materiales estimados de la cotización aprobada activa (*quote\_material\_items*). Si se selecciona uno, el sistema clasificará el gasto bajo la categoría **"Presupuestado"**.  
  * **Concepto:** Descripción del material. Se precargará automáticamente si se vinculó a un insumo estimado.  
  * **Detalles de la Transacción:** Tienda, Forma de Pago (Efectivo, Cheque, Tarjeta de Crédito, Transferencia Zelle) y Comprador.  
  * **Métricas de Compra:** Cantidad Real Adquirida, Costo Unitario Real y Subtotal Real (calculado como Cantidad \* Costo Unitario en modo de solo lectura).  
* **CA-26A.2 (Manejo de Gastos Excedentes):** Si se realiza una compra de un material que no estaba previsto en la cotización, se dejará el campo "Insumo Estimado Vinculado" vacío. El sistema registrará el gasto y lo clasificará de forma automática bajo la categoría **"Excedente / No Presupuestado"**.

### **HU-26B: Anulación Controlada de Compras de Materiales (Audit Trail)**

**Como:** Administrador Financiero

**Quiero:** Anular de forma lógica una compra de materiales errónea para que deje de computar en los costos reales del proyecto, manteniendo el registro físico con su respectiva justificación

**Para:** Mantener una contabilidad exacta y transparente, asegurando que todos los registros anulados tengan una explicación auditable en el sistema.

#### **Criterios de Aceptación Comerciales:**

* **CA-26B.1 (Bloqueo de DELETE físico):** En cumplimiento de la **RN-06**, el recurso de Filament `ProjectMaterialPurchaseResource` no contará con botones de eliminación física.  
* **CA-26B.2 (Modal de Anulación de Compra):** La interfaz proveerá una acción de *"Anular Compra"*. Al activarse, solicitará obligatoriamente un "Motivo de la Anulación". Al confirmar, se actualizarán los campos:  
  * `is_annulled` a *true*.  
  * `annulled_by_user_id` $\\leftarrow$ ID del usuario autenticado.  
  * `annulled_at` $\\leftarrow$ Timestamp actual.  
  * `annulment_reason` $\\leftarrow$ Motivo de la anulación ingresado por el usuario.  
* **CA-26B.3 (Recálculo Automático):** El costo de la compra anulada se restará inmediatamente del Costo Directo Real consolidado del Dashboard y del Balance de Caja del proyecto.

### **HU-27: Control de Anticipos y Flujo de Caja del Proyecto**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Registrar cada depósito, abono o pago que el cliente me entrega a lo largo de la obra

**Para:** Monitorear en tiempo real el flujo de caja del proyecto, conociendo cuánta liquidez operativa tengo y cuánto dinero resta por cobrar de la cotización aprobada.

#### **Criterios de Aceptación Comerciales:**

* **CA-27.1 (Registro Contable de Abonos):** El sistema proveerá un formulario limpio para registrar transacciones de ingresos en la tabla project\_deposits:  
  * **Monto Recibido:** Valor decimal positivo.  
  * **Método de Pago:** Cash, Check, Credit Card, Zelle.  
  * **Fecha de Recibo:** Fecha de ingreso (received\_at).  
  * **Referencia:** Campo de texto libre opcional para registrar el número de cheque o el ID de la transferencia bancaria.  
* **CA-27.2 (Cálculo del Balance de Caja de la Obra):** El sistema calculará en tiempo real el dinero líquido disponible en la caja del proyecto aplicando la fórmula financiera:  
  $$\\text{Balance de Caja del Proyecto} \= \\sum(\\text{project\\\_deposits.amount}) \- \\left\[ \\sum(\\text{project\\\_labor\\\_logs.actual\\\_subtotal}) \+ \\sum(\\text{project\\\_material\\\_purchases.actual\\\_subtotal}) \\right\]$$  
  *Este valor se mostrará de forma prominente en el tablero del proyecto, alertándome si la caja operativa cae a números negativos (es decir, cuando estoy gastando más en nómina y materiales de lo que el cliente me ha depositado).*

### **HU-28: Panel de Conciliación "Estimado vs. Real" (La Prueba del Nueve del Negocio)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Visualizar una pantalla de control y auditoría centralizada dentro de mi proyecto

**Para:** Contrastar de forma gráfica y numérica las desviaciones de costos directos, el Overhead real absorbido y la ganancia neta obtenida contra las proyecciones originales de la cotización aprobada.

#### **Criterios de Aceptación Comerciales:**

* **CA-28.1 (Establecimiento de la Línea Base Activa \- Escenario B):** Para realizar los cálculos comparativos, el sistema identificará la **única propuesta comercial que se encuentre en estado "Aprobada"** y vinculada al proyecto (projects.id). Cualquier cotización anterior en estado "Cerrada por Enmienda" o "Cancelada" será ignorada, garantizando que la comparación se realice siempre contra el último contrato legal vigente acordado con el cliente.  
* **CA-28.2 (Métricas de Desviación Financiera):** El panel de control en Filament renderizará tarjetas de indicadores (*Widgets*) que calcularán y contrastarán en tiempo real:  
  1. **Costo Directo ($CD$):**  
     * **Estimado ($CD\_{est}$):** El campo direct\_cost registrado en el snapshot de la cotización aprobada.  
     * **Real ($CD\_{real}$):** La suma consolidada del costo de nómina (project\_labor\_logs.actual\_subtotal) y compras (project\_material\_purchases.actual\_subtotal).  
  2. **Overhead Absorbido ($OH$):**  
     * **Estimado ($OH\_{est}$):** El campo overhead\_cost del snapshot de la cotización aprobada.  
     * **Real ($OH\_{real}$):** Las horas totales reales de esfuerzo trabajadas multiplicadas por la tasa de overhead que se congeló en el snapshot al aprobar el contrato:  
       $$OH\_{real} \= \\sum(\\text{Horas Reales de Jornadas}) \\times \\text{quotes.overhead\\\_rate\\\_applied}$$  
  3. **Utilidad Neta Real ($UN$):**  
     * **Estimada ($UN\_{est}$):** El margen bruto proyectado en dólares en el contrato:  
       $$UN\_{est} \= \\text{quotes.total\\\_price} \- (\\text{quotes.direct\\\_cost} \+ \\text{quotes.overhead\\\_cost})$$  
     * **Real ($UN\_{real}$):** La ganancia real de dinero que la empresa percibirá una vez absorbidos todos los costos directos e indirectos reales en el campo:  
       $$UN\_{real} \= \\text{quotes.total\\\_price} \- (CD\_{real} \+ OH\_{real})$$

### **HU-29: Cierre Operativo de Proyectos y Bloqueo de Bitácoras**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Cambiar el estado de un proyecto a "Finalizado" o "Cancelado"

**Para:** Impedir de forma automática e irreversible cualquier registro posterior de horas de mano de obra, compras de materiales o anticipos, protegiendo la veracidad de la auditoría financiera final del proyecto.

#### **Criterios de Aceptación Comerciales:**

* **CA-29.1 (Transición de Estado Manual):** El administrador podrá actualizar el estado del proyecto (`project_status_id`) de "En Ejecución" a **"Finalizado"** o **"Cancelado"** a través de Filament.  
* **CA-29.2 (Cierre Hermético de Bitácoras):** Al transicionar el proyecto a "Finalizado" o "Cancelado", el sistema bloqueará la base de datos (`PostgreSQL`) e impedirá la creación de nuevos registros en las tablas:  
  * `project_labor_logs`  
  * `project_material_purchases`  
  * `project_deposits`  
* **CA-29.3 (Bloqueo en Interfaz \- Filament):** Si un proyecto está finalizado o cancelado, los botones de "Registrar Jornada", "Registrar Compra" y "Registrar Anticipo" se ocultarán por completo de la interfaz de Filament de ese proyecto.  
* **CA-29.4 (Validación de API / Backend):** Si se intenta inyectar un gasto o anticipo a través de la API para un proyecto cerrado, el sistema responderá con un error HTTP `422 Unprocessable Content` con el mensaje: *"Acción denegada. El proyecto se encuentra Finalizado o Cancelado"*.

# Documento de Arquitectura de software

# **Documento de Arquitectura de Software**

**Proyecto:** Sistema de Simulación Financiera, Cotización Dinámica y Control de Márgenes Operativos (Ecosistema V1)

**Tecnología Core:** Laravel 13.x \+ Filament v3.x (PHP 8.3+)

**Patrón Arquitectónico:** Monolito Transaccional en Capas con Aislamiento de Lógica de Dominio

## **1\. Stack Tecnológico y Entorno de Ejecución**

* **Entorno de Ejecución:** PHP 8.3+ (exigido por Laravel 13, desbloqueando constantes de clase tipadas, la función nativa `json_validate()` y Attributes nativos de compilación).  
* **Framework Backend:** Laravel 13.x.  
* **Panel de Operación Administrativa:** Filament v3.x (componentes Blade interactivos con Livewire bajo el capó).  
* **Motor de Base de Datos:** PostgreSQL 16+.  
* **Gestor de Seguridad y Accesos:** Spatie Laravel Permission v6.x (autorización basada en Roles y Permisos granulares).  
* **Motor de Pruebas:** Pest PHP (v3) con estructura semántica de tests mediante bloques `describe` y `beforeEach`.  
* **Generación de Documentación de API:** Dedoc Scramble (OpenAPI 3.1), configurado para leer las firmas de los *JSON:API Resources* nativos de Laravel 13 sin anotaciones manuales.  
* **Procesamiento de Calendario Offline:** Librería local `azuyalabs/yasumi` para el cálculo matemático de festivos federales de EE. UU. (USA Federal Holidays) sin dependencias de red (RNF-1.1).

  ## **2\. Patrón de Flujo de Datos y Estructura de Capas**

El sistema procesará las solicitudes HTTP (tanto de la API de control como de las acciones internas de Filament) bajo el siguiente pipeline desacoplado:

\[Cliente HTTP / UI\] ➔ \[Request\] ➔ \[Policy\] ➔ \[Controller\] ➔ \[Service\] ➔ \[Resource\] 

### **A. Nomenclatura Estricta de Componentes (`ModeloAcciónTipodeArchivo`)**

Para garantizar la consistencia absoluta, cada archivo del sistema se nombrará bajo este formato. Ejemplo para la aprobación de una propuesta comercial:

* **Form Request:** `QuoteApproveRequest` (Valida que el input de aprobación sea correcto).  
* **Policy:** `QuotePolicy` (Valida que el usuario tenga el permiso Spatie `approve:quotes` y que la cotización esté en estado Borrador o Enviada).  
* **Controller:** `QuoteApproveController` (Controlador `__invoke` que recibe el request validado y llama al servicio).  
* **Service:** `QuoteApproveService` (Ejecuta la base de datos transaccional, genera el Snapshot y guarda los datos físicos).  
* **Resource:** `QuoteJsonResource` (Salida formateada de la cotización aprobada).

  ## **3\. Diccionarios de Dominio, Seguridad y Localización**

Para evitar los "strings mágicos" en el código, todos los estados, categorías y accesos se estructurarán en **Backed Enums** tipados, vinculados a archivos de traducción y control de seguridad.

app/Enums/

 ├── QuoteStatus.php        ➔ (draft, sent, approved, closed\_by\_amendment, canceled)

 ├── ProjectStatus.php      ➔ (draft, in\_progress, completed, canceled)

 ├── MaterialCategory.php   ➔ (budgeted, unbudgeted)

 ├── PaymentMethod.php      ➔ (cash, check, credit\_card, zelle)

 └── AppPermission.php      ➔ (manage\_settings, view\_any\_quotes, create\_quotes, approve\_quotes, edit\_quotes, write\_logs)

### **A. Catálogo de Permisos Granulares (`AppPermission`)**

Para evitar la dispersión de permisos en base de datos, toda acción sensible del sistema queda tipada de forma inmutable:

* `manage:settings` (Modificar tarifas base, multiplicadores de horas extras y costos fijos).  
* `approve:quotes` (Pasar cotizaciones a estado "Aprobada" y disparar el snapshot financiero).  
* `create:quotes` (Formular nuevas estimaciones y registrar materiales/mano de obra).  
* `create:enmiendas` (Generar enmiendas sobre cotizaciones aprobadas).  
* `write:logs` (Registrar gastos reales de campo, jornadas laborales y compras).  
* `close:projects` (Cambiar el estado de los proyectos a finalizados o cancelados).

  ### **B. Estrategia de Super-Administrador (Bypass de Desarrollo)**

Para acelerar el desarrollo del MVP sin configurar asignaciones de permisos complejas en local, el sistema implementará un interceptor global en `App\Providers\AppServiceProvider.php` (estilo Laravel 13):

PHP

use Illuminate\\Support\\Facades\\Gate;

public function boot(): void

{

    // Bypass automático: El rol "administrator" tiene acceso absoluto

    Gate::before(function ($user, $ability) {

        return $user-\>hasRole('administrator') ? true : null;

    });

}

## **4\. Estándar de Pipeline y Flujo en Capas**

Cada acción del sistema de cotización y balances se estructurará bajo el flujo estricto de cinco capas:

1. **Request (`app/Http/Requests/ModeloAcciónRequest.php`):** Form Request dedicado con tipado estricto y sanitización de inputs (ej. `QuoteApproveRequest`, `ProjectLaborLogStoreRequest`).  
2. **Policy (`app/Policies/ModeloPolicy.php`):** Clase de Laravel para autorizar la acción evaluando tanto el estado inmutable del modelo como el permiso del usuario mediante Spatie:  
3. PHP  
   public function approve(User $user, Quote $quote): bool  
   {  
       // 1\. Regla de Negocio: Solo cotizaciones no aprobadas ni cerradas  
       if (\! in\_array($quote-\>status\_id, \[QuoteStatus::DRAFT, QuoteStatus::SENT\])) {  
           return false;  
       }  
       // 2\. Regla de Seguridad Spatie RBAC  
       return $user-\>can('approve:quotes');  
   }  
4. **Controller (`app/Http/Controllers/ModeloAcciónController.php`):** Controlador Invocable de Acción Única (`__invoke`). Recibe el Request validado, invoca al Policy (`$this->authorize`), delega la lógica de cómputo al servicio correspondiente y devuelve el Resource (ej. `QuoteApproveController`).  
5. **Service (`app/Services/ModeloAcciónService.php`):** Clase pura de PHP encargada del cerebro matemático y transaccional. Es la única capa autorizada para tocar la base de datos de manera transaccional (`DB::transaction`) y realizar redondeos (ej. `QuoteApproveService`).  
6. **Resource (`app/Http/Resources/ModeloJsonResource.php`):** Serializa la respuesta JSON utilizando el estándar nativo JSON:API (ej. `QuoteJsonResource`).

   ### **5\. Suite de Pruebas Automatizadas con Pest PHP**

Se configurará el entorno de pruebas automatizadas en `tests/Pest.php` centralizando las dependencias globales de infraestructura, permitiendo que cada archivo de prueba (`Feature/Unit`) contenga bloques `describe()` limpios dedicados exclusivamente al montaje de datos del dominio de negocio:

#### **A. Configuración Global de Infraestructura (`tests/Pest.php`)**

PHP

\<?php

use Tests\\TestCase;

use Illuminate\\Foundation\\Testing\\RefreshDatabase;

// Extensión del caso de prueba y Traits de limpieza de base de datos

pest()-\>extend(TestCase::class)

    \-\>use(RefreshDatabase::class)

    \-\>beforeEach(function () {

        // Inicialización obligatoria de catálogos e infraestructura inmutable del sistema

        $this-\>seed(RolesAndPermissionsSeeder::class); // Spatie Roles y Permisos Base

        $this-\>seed(ProjectStatusesSeeder::class);     // Catálogo de estados de proyectos

        $this-\>seed(QuoteStatusesSeeder::class);       // Catálogo de estados de cotizaciones

        $this-\>seed(GlobalSettingsSeeder::class);      // Registro inicial ID 1 con multiplicadores fijos

    })

    \-\>in('Feature', 'Unit');

#### **B. Estructura de Pruebas Semánticas de Dominio (Ejemplo de Implementación Local)**

PHP

\<?php

use App\\Enums\\QuoteStatus;

use App\\Models\\Quote;

use App\\Models\\LaborRole;

describe('MOTOR DE SIMULACIÓN FINANCIERA \- REACTIVIDAD Y TOTALES', function () {

    beforeEach(function () {

        // Arrange Local: Creación de registros específicos para el contexto de este archivo

        $this-\>painterRole \= LaborRole::create(\[

            'name' \=\> 'Pintor Principal',

            'base\_salary' \=\> 20.0000,

            'social\_load\_pct' \=\> 15.0000, // Costo cargado automático C\_ch \= 23.0000

        \]);

        

        $this-\>quoteBorrador \= Quote::factory()-\>create(\[

            'status\_id' \=\> QuoteStatus::DRAFT-\>value,

            'margin\_applied' \=\> 20.0000

        \]);

    });

    test('el sistema calcula en caliente el costo directo sumando mano de obra y materiales', function () {

        // Act & Assert del cálculo reactivo financiero...

    });

});

## **6\. Integraciones y Documentación Automática**

* **Cálculo de Días Festivos (Yasumi ^2.11):** Integrado de forma offline en el cargador de servicios para determinar la capacidad promedio laboral de horas restando fines de semana y feriados de EE. UU.  
* **Documentación Viva de API (Dedoc Scramble):** Escanea por análisis estático la salida de tus controllers de acción única y firmas de `FormRequests` para exponer la especificación interactiva en OpenAPI 3.1 sin ensuciar tus clases con comentarios PHPDoc masivos.

  ## **7\. Requerimientos No Funcionales (RNF) \- Nivel Ingeniería**

* **RNF-1 (Precisión Aritmética Contable):** El sistema no debe utilizar variables de punto flotante nativas del hardware para cálculos agregados o comparativos. Todos los cálculos matemáticos del backend deben realizarse con precisión de cuatro decimales utilizando el estándar arbitrario de precisión decimal.  
* **RNF-2 (Rendimiento de Agregaciones en Caliente):** Las consultas sumatorias (`SUM`) necesarias para renderizar el Dashboard de Conciliación y el Balance de Caja deben ejecutarse en un tiempo inferior a 50 ms. La base de datos debe indexar físicamente las claves foráneas del proyecto (`project_id`) en todas las bitácoras de gastos y cobros.  
* **RNF-3 (Aislamiento de Modificación Financiera \- Snapshots):** Las actualizaciones en los catálogos de sueldos, el overhead mensual global de la empresa o las tarifas de materiales nunca deben recalcular o afectar los datos económicos persistidos físicamente en cotizaciones aprobadas, enviadas o cerradas por enmienda.  
* **RNF-4 (Seguridad Transaccional Multitabla):** Los flujos críticos que afecten la consistencia de múltiples tablas (como el Snapshot al aprobar o las transiciones en cascada de Enmiendas) deben estar envueltos en transacciones de base de datos con reversión automática (*rollback*) total ante cualquier error de red o base de datos.  
* **RNF-5 (Validación Hermética de Gastos):** La API y las interfaces de carga de datos en Filament deben desinfectar y validar las entradas impidiendo estrictamente el ingreso de montos monetarios o cantidades de insumos negativas, nulas o con caracteres inválidos.  
* **RNF-6 (Independencia Operativa Local):** El algoritmo de determinación del calendario laboral anual para estimaciones y tasas de overhead no debe depender de llamadas HTTP externas a APIs públicas, debiendo calcular la matriz de feriados federales de EE. UU. de forma 100% local en el servidor.  
* **RNF-7 (Cobertura de Pruebas Automatizadas):** El motor matemático financiero del sistema (clases de servicio de cálculo de cotizaciones y tasas) debe contar con una cobertura de pruebas de integración y unitarias automatizadas del 100%, garantizando la exactitud de los flujos de caja y márgenes de ganancia.  
* **RNF-8 (Estándar de Redondeo y Formato Visual):** Todas las cifras monetarias que se visualicen en el panel de control de Filament, tarjetas de métricas (*Widgets*), tablas administrativas y el documento PDF exportable de la propuesta comercial se formatearán de forma obligatoria a dos decimales utilizando el método de redondeo simétrico (*Half Up*).  
* **RNF-9 (Control de Acceso Basado en Roles y Permisos \- RBAC):** Toda acción que muta un estado financiero en base de datos o que modifique una configuración global debe estar validada por su respectivo `Policy` mapeado contra un permiso directo de Spatie. Filament debe estructurar sus recursos consumiendo de forma directa estas políticas, inhabilitando o escondiendo botones según los privilegios del usuario autenticado.

# Documento de Especificación Técnica

# **Especificación Técnica de Implementación**

## **1\. Esquema Físico de Base de Datos y Restricciones**

Para cumplir estrictamente con las reglas de **Precisión Contable Decimal** (RNF-2.3, RNF-4.3) y la **Regla de Preservación Histórica Lógica** (Regla 5.3), las migraciones de Laravel se diseñarán bajo los siguientes estándares físicos:

### **A. Precisión Monetaria Estricta**

Todas las columnas de base de datos destinadas a precios unitarios, subtotales, totales, salarios, y tasas de overhead deben definirse con el tipo de dato decimal(12, 4\) en las migraciones de PostgreSQL:

PHP  
$table-\>decimal('hourly\_cost', 12, 4); // C\_ch de roles de trabajo  
$table-\>decimal('overhead\_rate\_applied', 12, 4); // T\_oh congelada del snapshot  
$table-\>decimal('actual\_subtotal', 12, 4); // Gastos reales de mano de obra

### **B. Estrategia de Borrado e Integridad Referencial**

Para evitar la eliminación accidental de datos con implicaciones contables, las claves foráneas de tablas maestras utilizarán la restricción restrictOnDelete() en lugar de eliminaciones en cascada:

PHP  
// En la migración de quotes:  
$table-\>foreignId('project\_id')-\>constrained()-\>restrictOnDelete();  
$table-\>foreignId('status\_id')-\>constrained('quote\_statuses')-\>restrictOnDelete();

// En la migración de project\_labor\_logs:  
$table-\>foreignId('employee\_id')-\>constrained()-\>restrictOnDelete();

*Si se intenta eliminar un empleado o proyecto que ya tiene registros reales de ejecución, la base de datos lanzará una excepción a nivel de base de datos, blindando la integridad.*

### **C. Indexación Estratégica para Agregaciones (\<50ms)**

Para cumplir con el requerimiento **RNF-4.1**, se definirán índices explícitos en las columnas utilizadas frecuentemente en cláusulas WHERE y operaciones de agregación SUM dentro de nuestro panel comparativo de conciliación:

PHP  
$table-\>index(\['project\_id', 'purchased\_at'\]); // Para sumas de materiales reales  
$table-\>index(\['project\_id', 'logged\_at'\]); // Para sumas de nóminas reales  
$table-\>index(\['project\_id', 'received\_at'\]); // Para sumas de anticipos reales

## **2\. Transacciones y Consistencia de Eventos**

### **A. Transaccionalidad Atómica (Snapshots y Enmiendas)**

Cualquier proceso que involucre cambios de estado financiero múltiple debe envolverse en transacciones de base de datos (DB::transaction). Si la inyección del Snapshot de tarifas en la tabla quote\_labor\_assignments falla, se debe aplicar un rollback total automático para impedir que la cotización quede en estado "Aprobada" con datos financieros incompletos o corruptos (RNF-2.2).

### **B. Observadores de Eloquent (Eloquent Observers) para Overhead**

El recálculo automático de la Tasa de Overhead Global ($T\_{oh}$) se resolverá de forma desacoplada mediante un Observador en el modelo de gastos fijos:

* **Gatillo:** Cambios en la tabla fixed\_expenses (creación, edición, activación/desactivación de gastos).  
* **Acción del Observer:** Invalida el valor cacheado de la tasa de overhead y dispara el recálculo matemático utilizando la capacidad promedio calculada por el motor de Yasumi, guardando el resultado de forma atómica en global\_settings (CA-04.2).

## **3\. Estándar de Pruebas Automatizadas (Pest PHP)**

Para cumplir con el **100% de cobertura en cálculos críticos** (RNF-1.3, RNF-2.4), las pruebas automatizadas se estructurarán de manera estrictamente metodológica bajo el estándar Pest:

### **A. Ciclo de Vida del Entorno de Pruebas**

El archivo tests/Pest.php se configurará para utilizar el trait LazyRefreshDatabase de Laravel. Esto garantiza que la base de datos de pruebas se limpie de forma automática entre cada test individual.

### **B. Inicialización mediante Seeders en beforeEach**

Dado que el flujo de cotizaciones y proyectos depende de parámetros inmutables y catálogos estables, cada bloque de pruebas utilizará un gancho de preparación para asegurar un entorno real y constante:

* **beforeEach global o por grupo:** Se ejecutará el seeder maestro (DatabaseSeeder) que poblará los estados obligatorios (quote\_statuses, material\_categories) y los parámetros globales iniciales de la empresa (global\_settings) antes de correr las simulaciones.

### **C. Estructura de Pruebas Semánticas (describe)**

Los archivos de prueba estructurarán los casos de uso agrupando la funcionalidad por contextos semánticos mediante bloques describe() para facilitar la auditoría técnica. Ejemplo para el motor matemático:

PHP  
describe('Motor de Cálculo de Cotizaciones', function () {  
    beforeEach(function () {  
        $this-\>artisan('db:seed'); // Inicializa estados y settings de la empresa  
    });

    it('calcula la tasa de overhead por hora justa basada en Yasumi', function () {  
        // Simulación y aserciones  
    });

    it('bloquea la cotización al pasar a estado aprobada y genera snapshot', function () {  
        // Simulación y aserciones de snapshots físicos  
    });  
});
