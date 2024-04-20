<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagrama de Secuencia</title>

    <!-- Agrega el enlace a los archivos CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Agrega el enlace a los archivos JavaScript de Bootstrap (jQuery y Popper.js) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/gojs/style.css') }}" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar navbar-dark bg-info justify-content-center">
        <div class="container">
            <a class="navbar-brand" href="#">Diagramador</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ url('/home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ url('/diagramas') }}">Volver</a>
                    </li>
                    <form id="guardarDiagramaForm" method="post" action="{{ url('/diagramas/pizarra') }}">
                        <input type="hidden" name="diagram_id" value="{{ $diagram->id }}">
                        @csrf
                        <input type="hidden" name="contenidoJson" id="mySavedModel" value="">
                        <button class="btn btn-sm btn-success" type="button" id="guardarDiagramaButton">Guardar
                            Diagrama</button>
                    </form>


                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="generateCodePython" data-toggle="modal"
                            >Python</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="generateCodeJava" data-toggle="modal"
                            >Java</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="generateCodeJavaScript" data-toggle="modal"
                            >JavaScript</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="md:flex flex-col md:flex-row md:min-h-screen w-full max-w-screen-xl mx-auto">
        <script src="{{ asset('js/gojs/go.js') }}"></script>
        <div id="allSampleContent" class="p-4 w-full">
            <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>

            <script id="code">
                //FUNCION INIT
                function init() {
                    const $ = go.GraphObject.make;
                    myDiagram = new go.Diagram("myDiagramDiv", // must be the ID or reference to an HTML DIV
                        {
                            allowCopy: false,
                            linkingTool: $(MessagingTool), // defined below
                            "resizingTool.isGridSnapEnabled": true,
                            draggingTool: $(MessageDraggingTool), // defined below
                            "draggingTool.gridSnapCellSize": new go.Size(1, MessageSpacing / 4),
                            "draggingTool.isGridSnapEnabled": true,
                            // automatically extend Lifelines as Activities are moved or resized
                            "SelectionMoved": ensureLifelineHeights,
                            "PartResized": ensureLifelineHeights,
                            "undoManager.isEnabled": true
                        });

                    // cuando se modifique el documento, añada un "*" al título y active el botón "Guardar".
                    myDiagram.addDiagramListener("Modified", e => {
                        const button = document.getElementById("SaveButton");
                        if (button) button.disabled = !myDiagram.isModified;
                        const idx = document.title.indexOf("*");
                        if (myDiagram.isModified) {
                            if (idx < 0) document.title += "*";
                            saveDiagramAutomatically();
                        } else {
                            if (idx >= 0) document.title = document.title.slice(0, idx);
                        }
                    });

                    // define the Lifeline Node template.
                    myDiagram.groupTemplate =
                        $(go.Group, "Vertical", {
                                locationSpot: go.Spot.Bottom,
                                locationObjectName: "HEADER",
                                minLocation: new go.Point(0, 0),
                                maxLocation: new go.Point(9999, 0),
                                selectionObjectName: "HEADER"
                            },
                            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
                            $(go.Panel, "Auto", {
                                    name: "HEADER"
                                },
                                $(go.Shape, "Rectangle", {
                                    fill: $(go.Brush, "Linear", {
                                        0: "#bbdefb",
                                        1: go.Brush.darkenBy("#bbdefb", 0.1)
                                    }),
                                    stroke: null
                                }),
                                $(go.TextBlock, {
                                        margin: 5,
                                        font: "400 10pt Source Sans Pro, sans-serif",
                                        editable: true, // Habilitar la edición de texto
                                        textAlign: "center" // Centrar el texto
                                    },
                                    new go.Binding("text", "text").makeTwoWay())
                            ),
                            $(go.Shape, {
                                    figure: "LineV",
                                    fill: null,
                                    stroke: "gray",
                                    strokeDashArray: [3, 3],
                                    width: 1,
                                    alignment: go.Spot.Center,
                                    portId: "",
                                    fromLinkable: true,
                                    fromLinkableDuplicates: true,
                                    toLinkable: true,
                                    toLinkableDuplicates: true,
                                    cursor: "pointer"
                                },
                                new go.Binding("height", "duration", computeLifelineHeight))
                        );

                    // define the Activity Node template
                    myDiagram.nodeTemplate =
                        $(go.Node, {
                                locationSpot: go.Spot.Top,
                                locationObjectName: "SHAPE",
                                minLocation: new go.Point(NaN, LinePrefix - ActivityStart),
                                maxLocation: new go.Point(NaN, 19999),
                                selectionObjectName: "SHAPE",
                                resizable: true,
                                resizeObjectName: "SHAPE",
                                resizeAdornmentTemplate: $(go.Adornment, "Spot",
                                    $(go.Placeholder),
                                    $(go.Shape, // only a bottom resize handle
                                        {
                                            alignment: go.Spot.Bottom,
                                            cursor: "col-resize",
                                            desiredSize: new go.Size(6, 6),
                                            fill: "yellow"
                                        })
                                )
                            },
                            new go.Binding("location", "", computeActivityLocation).makeTwoWay(backComputeActivityLocation),
                            $(go.Shape, "Rectangle", {
                                    name: "SHAPE",
                                    fill: "white",
                                    stroke: "black",
                                    width: ActivityWidth,
                                    // allow Activities to be resized down to 1/4 of a time unit
                                    minSize: new go.Size(ActivityWidth, computeActivityHeight(0.25))
                                },
                                new go.Binding("height", "duration", computeActivityHeight).makeTwoWay(backComputeActivityHeight)),
                        );

                    // define the Message Link template.
                    myDiagram.linkTemplate =
                        $(MessageLink, // defined below
                            {
                                selectionAdorned: true,
                                curviness: 0
                            },
                            $(go.Shape, "Rectangle", {
                                stroke: "black"
                            }),
                            $(go.Shape, {
                                toArrow: "OpenTriangle",
                                stroke: "black"
                            }),
                            $(go.TextBlock, {
                                    font: "400 9pt Source Sans Pro, sans-serif",
                                    segmentIndex: 0,
                                    segmentOffset: new go.Point(NaN, NaN),
                                    isMultiline: false,
                                    editable: true
                                },
                                new go.Binding("text", "text").makeTwoWay())
                        );
                    // create the graph by reading the JSON data saved in "mySavedModel" textarea element
                    load();
                }
                // FIN INIT
                //LINEA DE VIDA BOTON
                function addLifeline() {
                    const model = myDiagram.model;
                    const nextKey = getNextLifelineKey(model);
                    const newLifelineData = {
                        key: nextKey,
                        text: "OBJETO",
                        isGroup: true,
                        loc: "400 0",
                        duration: 10,
                    };

                    model.addNodeData(newLifelineData);
                    saveDiagramAutomatically();
                }

                function getNextLifelineKey(model) {
                    let nextKey = 1;
                    while (model.findNodeDataForKey(nextKey)) {
                        nextKey++;
                    }
                    return nextKey.toString();
                }

                function ensureLifelineHeights(e) {
                    // iterate over all Activities (ignore Groups)
                    const arr = myDiagram.model.nodeDataArray;
                    let max = -1;
                    for (let i = 0; i < arr.length; i++) {
                        const act = arr[i];
                        if (act.isGroup) continue;
                        max = Math.max(max, act.start + act.duration);
                    }
                    if (max > 0) {
                        // now iterate over only Groups
                        for (let i = 0; i < arr.length; i++) {
                            const gr = arr[i];
                            if (!gr.isGroup) continue;
                            if (max > gr.duration) { // this only extends, never shrinks
                                myDiagram.model.setDataProperty(gr, "duration", max);
                            }
                        }
                    }
                }

                // some parameters
                const LinePrefix = 20; // vertical starting point in document for all Messages and Activations
                const LineSuffix = 30; // vertical length beyond the last message time
                const MessageSpacing = 20; // vertical distance between Messages at different steps
                const ActivityWidth = 10; // width of each vertical activity bar
                const ActivityStart = 5; // height before start message time
                const ActivityEnd = 5; // height beyond end message time

                function computeLifelineHeight(duration) {
                    return LinePrefix + duration * MessageSpacing + LineSuffix;
                }

                function computeActivityLocation(act) {
                    const groupdata = myDiagram.model.findNodeDataForKey(act.group);
                    if (groupdata === null) return new go.Point();
                    // get location of Lifeline's starting point
                    const grouploc = go.Point.parse(groupdata.loc);
                    return new go.Point(grouploc.x, convertTimeToY(act.start) - ActivityStart);
                }

                function backComputeActivityLocation(loc, act) {
                    myDiagram.model.setDataProperty(act, "start", convertYToTime(loc.y + ActivityStart));
                }

                function computeActivityHeight(duration) {
                    return ActivityStart + duration * MessageSpacing + ActivityEnd;
                }

                function backComputeActivityHeight(height) {
                    return (height - ActivityStart - ActivityEnd) / MessageSpacing;
                }

                // time is just an abstract small non-negative integer
                // here we map between an abstract time and a vertical position
                function convertTimeToY(t) {
                    return t * MessageSpacing + LinePrefix;
                }

                function convertYToTime(y) {
                    return (y - LinePrefix) / MessageSpacing;
                }

                // a custom routed Link
                class MessageLink extends go.Link {
                    constructor() {
                        super();
                        this.time = 0; // use this "time" value when this is the temporaryLink
                    }

                    getLinkPoint(node, port, spot, from, ortho, othernode, otherport) {
                        const p = port.getDocumentPoint(go.Spot.Center);
                        const r = port.getDocumentBounds();
                        const op = otherport.getDocumentPoint(go.Spot.Center);

                        const data = this.data;
                        const time = data !== null ? data.time : this
                            .time; // if not bound, assume this has its own "time" property

                        const aw = this.findActivityWidth(node, time);
                        const x = (op.x > p.x ? p.x + aw / 2 : p.x - aw / 2);
                        const y = convertTimeToY(time);
                        return new go.Point(x, y);
                    }

                    findActivityWidth(node, time) {
                        let aw = ActivityWidth;
                        if (node instanceof go.Group) {
                            // see if there is an Activity Node at this point -- if not, connect the link directly with the Group's lifeline
                            if (!node.memberParts.any(mem => {
                                    const act = mem.data;
                                    return (act !== null && act.start <= time && time <= act.start + act.duration);
                                })) {
                                aw = 0;
                            }
                        }
                        return aw;
                    }

                    getLinkDirection(node, port, linkpoint, spot, from, ortho, othernode, otherport) {
                        const p = port.getDocumentPoint(go.Spot.Center);
                        const op = otherport.getDocumentPoint(go.Spot.Center);
                        const right = op.x > p.x;
                        return right ? 0 : 180;
                    }

                    computePoints() {
                        if (this.fromNode === this.toNode) { // also handle a reflexive link as a simple orthogonal loop
                            const data = this.data;
                            const time = data !== null ? data.time : this
                                .time; // if not bound, assume this has its own "time" property
                            const p = this.fromNode.port.getDocumentPoint(go.Spot.Center);
                            const aw = this.findActivityWidth(this.fromNode, time);

                            const x = p.x + aw / 2;
                            const y = convertTimeToY(time);
                            this.clearPoints();
                            this.addPoint(new go.Point(x, y));
                            this.addPoint(new go.Point(x + 50, y));
                            this.addPoint(new go.Point(x + 50, y + 5));
                            this.addPoint(new go.Point(x, y + 5));
                            return true;
                        } else {
                            return super.computePoints();
                        }
                    }
                }


                // A custom LinkingTool that fixes the "time" (i.e. the Y coordinate)
                // for both the temporaryLink and the actual newly created Link
                class MessagingTool extends go.LinkingTool {
                    constructor() {
                        super();
                        // Since 2.2 you can also author concise templates with method chaining instead of GraphObject.make
                        // For details, see https://gojs.net/latest/intro/buildingObjects.html
                        const $ = go.GraphObject.make;
                        this.temporaryLink =
                            $(MessageLink,
                                $(go.Shape, "Rectangle", {
                                    stroke: "magenta",
                                    strokeWidth: 2
                                }),
                                $(go.Shape, {
                                    toArrow: "OpenTriangle",
                                    stroke: "magenta"
                                }));
                    }

                    doActivate() {
                        super.doActivate();
                        const time = convertYToTime(this.diagram.firstInput.documentPoint.y);
                        this.temporaryLink.time = Math.ceil(time); // round up to an integer value
                    }

                    insertLink(fromnode, fromport, tonode, toport) {
                        const newlink = super.insertLink(fromnode, fromport, tonode, toport);
                        if (newlink !== null) {
                            const model = this.diagram.model;
                            // specify the time of the message
                            const start = this.temporaryLink.time;
                            const duration = 1;
                            newlink.data.time = start;
                            model.setDataProperty(newlink.data, "text", "msg");
                            // and create a new Activity node data in the "to" group data
                            const newact = {
                                group: newlink.data.to,
                                start: start,
                                duration: duration
                            };
                            model.addNodeData(newact);
                            // now make sure all Lifelines are long enough
                            ensureLifelineHeights();
                            saveDiagramAutomatically();
                        }
                        return newlink;
                    }
                }


                class MessageDraggingTool extends go.DraggingTool {
                    computeEffectiveCollection(parts, options) {
                        const result = super.computeEffectiveCollection(parts, options);
                        result.add(new go.Node(), new go.DraggingInfo(new go.Point()));
                        parts.each(part => {
                            if (part instanceof go.Link) {
                                result.add(part, new go.DraggingInfo(part.getPoint(0).copy()));
                            }
                        })
                        saveDiagramAutomatically();
                        return result;
                    }

                    // override to allow dragging when the selection only includes Links
                    mayMove() {
                        return !this.diagram.isReadOnly && this.diagram.allowMove;
                    }

                    moveParts(parts, offset, check) {
                        super.moveParts(parts, offset, check);
                        const it = parts.iterator;
                        while (it.next()) {
                            if (it.key instanceof go.Link) {
                                const link = it.key;
                                const startY = it.value.point.y; // DraggingInfo.point.y
                                let y = startY + offset.y; // determine new Y coordinate value for this link
                                const cellY = this.gridSnapCellSize.height;
                                y = Math.round(y / cellY) * cellY; // snap to multiple of gridSnapCellSize.height
                                const t = Math.max(0, convertYToTime(y));
                                link.diagram.model.set(link.data, "time", t);
                                link.invalidateRoute();
                            }
                        }
                    }
                }
                var contenidoJson = {!! json_encode($contenidoJson) !!};
                console.log(contenidoJson)

                // Show the diagram's model in JSON format
                function save() {
                    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
                    myDiagram.isModified = false;
                }

                function load() {
                    const jsonContent = {!! json_encode($contenidoJson) !!};
                    if (jsonContent) {
                        myDiagram.model = go.Model.fromJson(jsonContent);
                    } else {
                        // Manejar el caso en el que el contenido JSON sea nulo o inválido
                        console.error("El contenido JSON es nulo o inválido.");
                    }
                }

                window.addEventListener('DOMContentLoaded', init);
            </script>

            <div id="sample">
                <div id="myDiagramDiv" style="border: solid 1px black; width: 100%; height: 450px"></div>
                <div>
                    <div>
                        <button id="AddLifelineButton" onclick="addLifeline()">Añadir Linea de Vida</button>
                        <input id="Delete" type="button" onclick="myDiagram.commandHandler.deleteSelection()" value="Eliminar ">
                        <input id="Undo" type="button" onclick="myDiagram.commandHandler.undo()" value="Deshacer ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
   
   function checkUpdates() {
    $.ajax({
        url: "{{route('pizarra.actualizar',['diagram' => $diagram->id])}}",
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const jsonContent = {!! json_encode($contenidoJson) !!};
            // Manejar los cambios recibidos en 'response'
            console.log(response);
            // Ejemplo: Actualizar la tabla HTML con los datos recibidos
            myDiagram.model = go.Model.fromJson(jsonContent);
            $('#table-container').html(response.table_html);
            
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

$(document).ready(function() {
    // Ejecutar la función checkUpdates() cada 5 segundos
    //setInterval(checkUpdates, 5000);
    
});
   
</script>
<script>
    // FUNCION PARA GENERAR CODIGO DE JAVA
    function generateJavaCodeFromDiagram(diagramJson) {
        // Analizar el JSON del diagrama
        const diagramData = JSON.parse(diagramJson);

        // Crear una cadena para el código Java
        let javaCode = "public class Main {\n";

        // Crear un mapa para asociar nombres de clases basados en el texto a las claves
        const textToClassName = {};

        // Crear instancias de clases basadas en la propiedad "text"
        diagramData.nodeDataArray.forEach(node => {
            const text = node.text;
            if (text) {
                const className = text.replace(/\s+/g, '');
                textToClassName[node.key] = className;
                javaCode += `    public ${className} ${text.toLowerCase()} = new ${className}();\n`;
            }
        });

        // Generar llamadas a métodos dentro de la clase main
        diagramData.linkDataArray.forEach(link => {
            const fromNode = diagramData.nodeDataArray.find(node => node.key === link.from);
            const toNode = diagramData.nodeDataArray.find(node => node.key === link.to);

            if (fromNode && fromNode.text && toNode && toNode.text) {
                const fromText = fromNode.text;
                const toText = toNode.text;
                const methodName = link.text.replace(/\s+/g, ''); // Eliminar espacios en el nombre del método
                javaCode += `    ${fromText.toLowerCase()}.${methodName}();\n`;
            }
        });

        // Cerrar la clase Main
        javaCode += "}\n";

        // Crear clases para representar elementos del diagrama y generar métodos vacíos
        Object.keys(textToClassName).forEach(key => {
            const className = textToClassName[key];
            javaCode += `class ${className} {\n`;

            diagramData.linkDataArray.forEach(link => {
                const fromKey = link.from;
                const toKey = link.to;

                if (fromKey === key) {
                    const methodName = link.text.replace(/\s+/g,
                        ''); // Eliminar espacios en el nombre del método
                    javaCode += `    public void ${methodName}() {\n`;
                    javaCode += "        // Implementación de la operación " + methodName + "\n";
                    javaCode += "    }\n";
                }
            });

            javaCode += "}\n";
        });

        return javaCode;
    }


    // FUNCION PARA GENERAR CODIGO DE PYTHON
    function generatePythonCodeFromDiagram(diagramJson) {
        // Analizar el JSON del diagrama
        const diagramData = JSON.parse(diagramJson);

        // Crear una cadena para el código Python
        let pythonCode = "";

        // Crear clases para representar elementos del diagrama y generar métodos vacíos
        const textToClassName = {};
        diagramData.nodeDataArray.forEach(node => {
            const text = node.text;
            if (text) {
                const className = text.replace(/\s+/g, '');
                textToClassName[node.key] = className;
                pythonCode += `class ${className}:\n`;

                diagramData.linkDataArray.forEach(link => {
                    const fromKey = link.from;
                    const toKey = link.to;

                    if (fromKey === node.key) {
                        const methodName = link.text.replace(/\s+/g,
                            ''); // Eliminar espacios en el nombre del método
                        pythonCode += `    def ${methodName}(self):\n`;
                        pythonCode += "        # Implementación de la operación " + methodName + "\n\n";
                    }
                });

                pythonCode += "\n";
            }
        });

        // Generar llamadas a métodos dentro del código principal
        pythonCode += "if __name__ == '__main__':\n";
        pythonCode += "    # Crear instancias de clases\n";
        Object.keys(textToClassName).forEach(key => {
            const className = textToClassName[key];
            pythonCode += `    ${className.toLowerCase()} = ${className}()\n`;
        });

        pythonCode += "\n";
        pythonCode += "    # Generar llamadas a métodos\n";
        diagramData.linkDataArray.forEach(link => {
            const fromNode = diagramData.nodeDataArray.find(node => node.key === link.from);
            const toNode = diagramData.nodeDataArray.find(node => node.key === link.to);

            if (fromNode && fromNode.text && toNode && toNode.text) {
                const fromText = fromNode.text;
                const toText = toNode.text;
                const methodName = link.text.replace(/\s+/g, ''); // Eliminar espacios en el nombre del método
                pythonCode += `    ${fromText.toLowerCase()}.${methodName}()\n`;
            }
        });

        return pythonCode;
    }

    //FUNCION PARA GENERAR CODIGO DE JAVASCRIPT
    function generateJavaScriptCodeFromDiagram(diagramJson) {
        // Analizar el JSON del diagrama
        const diagramData = JSON.parse(diagramJson);

        // Crear una cadena para el código JavaScript
        let jsCode = '';

        // Crear clases para representar elementos del diagrama y generar métodos vacíos
        const textToClassName = {};

        diagramData.nodeDataArray.forEach(node => {
            const text = node.text;
            if (text) {
                const className = text.replace(/\s+/g, '');
                textToClassName[node.key] = className;
                jsCode += `class ${className} {\n`;
                jsCode += "    constructor() {\n";
                jsCode += "        // Constructor\n";
                jsCode += "    }\n";

                diagramData.linkDataArray.forEach(link => {
                    const fromKey = link.from;
                    const toKey = link.to;

                    if (fromKey === node.key) {
                        const methodName = link.text.replace(/\s+/g, '');
                        jsCode += `    ${methodName}() {\n`;
                        jsCode += "        // Implementación de la operación " + methodName + "\n";
                        jsCode += "    }\n";
                    }
                });

                jsCode += "}\n";
            }
        });
        return jsCode;
    }
    //DESCARGA EL CODIGO
    function generateFile(content, filename) {

        const element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    }

    /*const codejava = generateJavaCodeFromDiagram(contenidoJson);
    const codepython = generatePythonCodeFromDiagram(contenidoJson);
    const codejavascript = generateJavaScriptCodeFromDiagram(contenidoJson);*/
    // console.log(codejava);
    // console.log(codepython);
    // console.log(codejavascript);

    /*function displayGeneratedCode() {

        var contenidoJson = myDiagram.model.toJson();

        const codejava = generateJavaCodeFromDiagram(contenidoJson);
        const codepython = generatePythonCodeFromDiagram(contenidoJson);
        const codejavascript = generateJavaScriptCodeFromDiagram(contenidoJson);

        document.getElementById("generatedJavaCode").textContent = codejava;
        document.getElementById("generatedPythonCode").textContent = codepython;
        document.getElementById("generatedJavaScriptCode").textContent = codejavascript;
    }*/
    //document.getElementById("generateCodeButton").addEventListener("click", displayGeneratedCode);
    //Desacarga codigo Python
    function displayGeneratedCodePython(){
        var contenidoJson = myDiagram.model.toJson();

        const codepython = generatePythonCodeFromDiagram(contenidoJson);
        generateFile(codepython,"code.py");
    }
    document.getElementById("generateCodePython").addEventListener("click", displayGeneratedCodePython);

    //Descarga Codigo Java
    function displayGeneratedCodeJava(){
        var contenidoJson = myDiagram.model.toJson();

        const codejava = generateJavaCodeFromDiagram(contenidoJson);
        generateFile(codejava,"code.java");
    }
    document.getElementById("generateCodeJava").addEventListener("click", displayGeneratedCodeJava);

    //Descarga codigo JavaScript
    function displayGeneratedCodeJavaScript(){
        var contenidoJson = myDiagram.model.toJson();

        const codejavascript = generateJavaScriptCodeFromDiagram(contenidoJson);
        generateFile(codejavascript,"code.js");
    }
    document.getElementById("generateCodeJavaScript").addEventListener("click", displayGeneratedCodeJavaScript);
</script>

<script>
    function saveDiagramAutomatically() {
        // Obtén el contenido JSON del diagrama
        var contenidoJson = myDiagram.model.toJson();
        // Realiza la solicitud AJAX para guardar el diagrama
        $.ajax({
            type: 'POST',
            url: $('#guardarDiagramaForm').attr('action'),
            data: {
                _token: $('input[name="_token"]').val(),
                diagram_id: $('input[name="diagram_id"]').val(),
                contenidoJson: contenidoJson
            },
            success: function(response) {
                // Maneja la respuesta del servidor, si es necesario
                console.log('Diagrama guardado con éxito.');

            },
            error: function(error) {
                // Maneja errores, si es necesario
                console.error('Error al guardar el diagrama:', error);
            }
        });
    }



    // Llama a la función de guardado automáticamente cada segundo
    //setInterval(saveDiagramAutomatically, 3000);

    // Agrega el manejador al botón "Guardar Diagrama"
    $(document).ready(function() {
        $('#guardarDiagramaButton').click(function() {
            saveDiagramAutomatically(); // Llama al guardado manual

        });
    });


</script>

</html>
