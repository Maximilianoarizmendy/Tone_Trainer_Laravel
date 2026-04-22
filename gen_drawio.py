
import xml.etree.ElementTree as ET
import datetime

def create_drawio():
    mxfile = ET.Element('mxfile', host="Electron", modified=datetime.datetime.now().isoformat(), agent="Antigravity", version="21.6.8", type="device")
    diagram = ET.SubElement(mxfile, 'diagram', id="page1", name="Mapa de Navegación Tone Trainer")
    mxGraphModel = ET.SubElement(diagram, 'mxGraphModel', dx="1400", dy="1400", grid="1", gridSize="10", guides="1", tooltips="1", connect="1", arrows="1", fold="1", page="1", pageScale="1", pageWidth="1169", pageHeight="827", math="0", shadow="1")
    root = ET.SubElement(mxGraphModel, 'root')
    
    ET.SubElement(root, 'mxCell', id="0")
    ET.SubElement(root, 'mxCell', id="1", parent="0")

    # Node definitions: (label, x, y, w, h, color, fontColor)
    nodes = [
        # Public Area (0-2)
        ("Página de Inicio (Landing)", 480, 20, 200, 60, "#1A237E", "#FFFFFF"),
        ("Registro de Usuario", 330, 120, 150, 45, "#283593", "#FFFFFF"),
        ("Acceso (Login)", 630, 120, 150, 45, "#283593", "#FFFFFF"),
        
        # Dashboard Root (3)
        ("Dashboard (Inicio)", 480, 220, 200, 70, "#0D47A1", "#FFFFFF"),
        
        # Shared Dashboard Features (4-9)
        ("Resumen", 50, 350, 120, 45, "#1976D2", "#FFFFFF"),
        ("Entrenamiento", 180, 350, 120, 45, "#1976D2", "#FFFFFF"),
        ("Nutrición", 310, 350, 120, 45, "#1976D2", "#FFFFFF"),
        ("Mensajes", 440, 350, 120, 45, "#1976D2", "#FFFFFF"),
        ("Perfil", 570, 350, 120, 45, "#1976D2", "#FFFFFF"),
        ("Configuración", 700, 350, 120, 45, "#1976D2", "#FFFFFF"),
        
        # Role: User - Rol 1 (10-12)
        ("Área Personal (Usuario)", 200, 480, 180, 55, "#00695C", "#FFFFFF"),
        ("Progreso (Métricas)", 100, 600, 160, 45, "#00897B", "#FFFFFF"),
        ("Metas y Logros", 280, 600, 160, 45, "#00897B", "#FFFFFF"),
        
        # Role: Staff & Admin - Rol 2, 3, 4 (13-17)
        ("Gestión y Administración", 850, 480, 200, 55, "#4527A0", "#FFFFFF"),
        ("Panel Administrador (Rol 2)", 700, 600, 160, 45, "#5E35B1", "#FFFFFF"),
        ("Panel Nutricionista (Rol 3)", 870, 600, 170, 45, "#5E35B1", "#FFFFFF"),
        ("Panel Entrenador (Rol 4)", 1050, 600, 160, 45, "#5E35B1", "#FFFFFF"),
        
        # Shared Staff Feature (18)
        ("Gestión de Usuarios", 870, 720, 180, 55, "#37474F", "#FFFFFF"),
    ]

    for i, (label, x, y, w, h, color, fColor) in enumerate(nodes):
        cell_id = str(i + 2)
        style = f"rounded=1;whiteSpace=wrap;html=1;fillColor={color};strokeColor=none;fontColor={fColor};fontStyle=1;fontSize=12;shadow=1;glass=1;"
        cell = ET.SubElement(root, 'mxCell', id=cell_id, value=label, style=style, parent="1", vertex="1")
        ET.SubElement(cell, 'mxGeometry', {'x': str(x), 'y': str(y), 'width': str(w), 'height': str(h), 'as': 'geometry'})

    # Connections: (src_idx, tgt_idx)
    connections = [
        (0, 1), (0, 2), # Landing to Register/Login
        (2, 3),         # Login to Dashboard
        (3, 4), (3, 5), (3, 6), (3, 7), (3, 8), (3, 9), # Dashboard to Shared features
        (3, 10),        # Dashboard to User Section
        (10, 11), (10, 12), # User Section to features
        (3, 13),        # Dashboard to Staff Section
        (13, 14), (13, 15), (13, 16), # Staff Section to Panels
        (14, 17), (15, 17), (16, 17), # Panels to Shared User Management
    ]

    for i, (src_idx, tgt_idx) in enumerate(connections):
        edge_id = f"edge_{i}"
        source = str(src_idx + 2)
        target = str(tgt_idx + 2)
        style = "edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#455A64;strokeWidth=2;endArrow=block;endFill=1;"
        edge = ET.SubElement(root, 'mxCell', id=edge_id, value="", style=style, parent="1", source=source, target=target, edge="1")
        ET.SubElement(edge, 'mxGeometry', {'relative': '1', 'as': 'geometry'})

    tree = ET.ElementTree(mxfile)
    ET.indent(tree, space="  ", level=0)
    tree.write("c:/Users/maxig/Documents/Tone-Trainer-Laravel/mapanavegacion.drawio", encoding="utf-8", xml_declaration=True)

create_drawio()
