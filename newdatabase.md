# Database Schema for Manufacturing Execution System (MES)

Berikut adalah rancangan database untuk sistem Manufacturing Execution System (MES) berdasarkan analisis file `ProjectDetail.tsx`:

```dbdiagram
Table projects {
  id string [pk, not null]
  code string [not null]
  name string [not null]
  customer string [not null]
  deadline datetime [not null]
  totalQty integer [not null]
  unit string [not null]
  createdAt datetime
  updatedAt datetime
}

Table items {
  id string [pk, not null]
  projectId string [not null]
  name string [not null]
  dimensions string
  thickness string
  quantity integer [not null]
  qtySet integer [not null]
  unit string [not null]
  flowType string [not null] // 'OLD' | 'NEW'
  isBomLocked boolean [not null, default: false]
  isWorkflowLocked boolean [not null, default: false]
  bom json // array of BOM items
  workflow json // array of workflow steps
  warehouseQty integer [not null, default: 0]
  shippedQty integer [not null, default: 0]
  assemblyStats json // object with stats per assembly step
  createdAt datetime
  updatedAt datetime
}

Table subAssemblies {
  id string [pk, not null]
  itemId string [not null]
  name string [not null]
  qtyPerParent integer [not null]
  materialId string [not null]
  processes json [not null] // array of process steps
  totalNeeded integer [not null]
  completedQty integer [not null, default: 0]
  totalProduced integer [not null, default: 0]
  consumedQty integer [not null, default: 0]
  stepStats json // object with stats per step
  isLocked boolean [not null, default: false]
  createdAt datetime
  updatedAt datetime
}

Table machines {
  id string [pk, not null]
  name string [not null]
  code string [not null]
  type string [not null] // corresponds to process steps
  status string [not null, default: 'ACTIVE']
  createdAt datetime
  updatedAt datetime
}

Table materials {
  id string [pk, not null]
  name string [not null]
  code string [not null]
  description string
  unit string [not null]
  createdAt datetime
  updatedAt datetime
}

Table tasks {
  id string [pk, not null]
  projectId string [not null]
  itemId string [not null]
  subAssemblyId string // nullable, for sub-assembly tasks
  step string [not null] // process step name
  machineId string // nullable, assigned machine
  targetQty integer [not null]
  completedQty integer [not null, default: 0]
  status string [not null, default: 'PENDING'] // PENDING, IN_PROGRESS, COMPLETED
  createdAt datetime
  updatedAt datetime
}

Table logs {
  id string [pk, not null]
  taskId string [not null]
  itemId string [not null]
  subAssemblyId string // nullable
  step string [not null]
  operator string [not null]
  goodQty integer [not null]
  timestamp datetime [not null]
  notes text
  createdAt datetime
  updatedAt datetime
}

Ref: projects.id - items.projectId
Ref: items.id - subAssemblies.itemId
Ref: items.id - tasks.itemId
Ref: subAssemblies.id - tasks.subAssemblyId
Ref: machines.id - tasks.machineId
Ref: materials.id - subAssemblies.materialId
Ref: tasks.id - logs.taskId
Ref: items.id - logs.itemId
Ref: subAssemblies.id - logs.subAssemblyId
```

## Penjelasan Skema

### Entitas Utama:
1. **projects**: Menyimpan informasi proyek keseluruhan
2. **items**: Item-item kerja dalam proyek
3. **subAssemblies**: Komponen-komponen rakitan untuk item dengan flowType 'NEW'
4. **machines**: Mesin dan stasiun kerja yang tersedia
5. **materials**: Material yang digunakan dalam produksi
6. **tasks**: Tugas produksi untuk setiap tahapan
7. **logs**: Log aktivitas produksi

### Relasi:
- Satu proyek bisa memiliki banyak item
- Satu item bisa memiliki banyak sub-assembly
- Satu item bisa memiliki banyak tugas
- Satu sub-assembly bisa memiliki banyak tugas
- Satu mesin bisa ditugaskan ke banyak tugas
- Satu material bisa digunakan di banyak sub-assembly
- Satu tugas bisa memiliki banyak log
- Satu item bisa memiliki banyak log
- Satu sub-assembly bisa memiliki banyak log

Skema ini dirancang untuk mendukung sistem manufaktur yang kompleks dengan alur produksi yang terdiri dari tahapan raw steps dan assembly steps, serta mencakup manajemen inventory dan tracking produksi secara real-time.