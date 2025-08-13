const charts = {}
let employeesDataTable = null
const axios = window.axios 
const $ = window.jQuery

// Inicializar dashboard
document.addEventListener("DOMContentLoaded", () => {
  // Verificar autenticación
  if (!localStorage.getItem("jwt_token")) {
    window.location.href = "/login"
    return
  }

  const userData = JSON.parse(localStorage.getItem("user_data") || "{}")
  document.getElementById("userName").textContent = userData.name || "Usuario"

  initializeTabs()
  loadDashboardData()
  loadFilterOptions()

  document.getElementById("addEmployeeButton").addEventListener("click", openAddEmployeeModal)
  
  const form = document.getElementById("addEmployeeForm")
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault()

      const formData = new FormData(form)
      const employeeData = {}

      for (const [key, value] of formData.entries()) {
        employeeData[key] = value
      }

      try {
        const response = await axios.post("/dashboard/employees", employeeData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
            "Content-Type": "application/json",
          },
        })

        if (response.data.success) {
          alert("Empleado creado exitosamente")
          closeAddEmployeeModal()

          if (employeesDataTable) {
            employeesDataTable.ajax.reload()
          }

          loadDashboardData()
        }
      } catch (error) {
        console.error("Error creating employee:", error)
        if (error.response && error.response.data && error.response.data.errors) {
          const errors = error.response.data.errors
          let errorMessage = "Errores de validación:\n"
          for (const field in errors) {
            errorMessage += `- ${errors[field].join(", ")}\n`
          }
          alert(errorMessage)
        } else {
          alert("Error al crear el empleado. Por favor, intenta de nuevo.")
        }
      }
    })
  }
})

function initializeTabs() {
  switchTab("indicadores")
}

function switchTab(tabName) {
  document.querySelectorAll(".tab-content").forEach((content) => {
    content.classList.add("hidden")
  })

  // Remover clase active de todos los botones
  document.querySelectorAll(".tab-button").forEach((button) => {
    button.classList.remove("active", "border-indigo-500", "text-indigo-600")
    button.classList.add("border-transparent", "text-gray-500")
  })

  document.getElementById(`content-${tabName}`).classList.remove("hidden")

  const activeButton = document.getElementById(`tab-${tabName}`)
  activeButton.classList.add("active", "border-indigo-500", "text-indigo-600")
  activeButton.classList.remove("border-transparent", "text-gray-500")

  if (tabName === "detalle") {
    initializeEmployeesTable()
  }
}

async function loadFilterOptions() {
  try {
    const response = await axios.get("/dashboard/filter-options", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const citySelect = document.getElementById("filter-city")
    citySelect.innerHTML = '<option value="">Todas las ciudades</option>'
    data.cities.forEach((city) => {
      const option = document.createElement("option")
      option.value = city
      option.textContent = city
      citySelect.appendChild(option)
    })

    const educationSelect = document.getElementById("filter-education")
    educationSelect.innerHTML = '<option value="">Todas las educaciones</option>'
    data.educations.forEach((education) => {
      const option = document.createElement("option")
      option.value = education
      option.textContent = education
      educationSelect.appendChild(option)
    })
  } catch (error) {
    console.error("Error loading filter options:", error)
  }
}

function initializeEmployeesTable() {
  if (employeesDataTable) {
    employeesDataTable.destroy()
  }

  employeesDataTable = $("#employeesTable").DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: "/api/dashboard/employees/",
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
      dataSrc: "data",
    },
    columns: [
      { data: "id" },
      { data: "education" },
      { data: "joining_year" },
      { data: "city" },
      {
        data: "payment_tier",
        render: (data) =>
          `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Tier ${data}</span>`,
      },
      { data: "age" },
      {
        data: "gender",
        render: (data) => {
          const isMale = data === "Male"
          return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isMale ? "bg-blue-100 text-blue-800" : "bg-pink-100 text-pink-800"}">${isMale ? "M" : "F"}</span>`
        },
      },
      {
        data: "ever_benched",
        render: (data) =>
          `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data ? "bg-yellow-100 text-yellow-800" : "bg-green-100 text-green-800"}">${data ? "Sí" : "No"}</span>`,
      },
      {
        data: "experience_in_current_domain",
        render: (data) => `${data} años`,
      },
      {
        data: "leave_or_not",
        render: (data) =>
          `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data ? "bg-red-100 text-red-800" : "bg-green-100 text-green-800"}">${data ? "En riesgo" : "Estable"}</span>`,
      },
    ],
    language: {
      processing: "Procesando...",
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ empleados",
      info: "Mostrando _START_ a _END_ de _TOTAL_ empleados",
      infoEmpty: "Mostrando 0 a 0 de 0 empleados",
      infoFiltered: "(filtrado de _MAX_ empleados totales)",
      loadingRecords: "Cargando...",
      zeroRecords: "No se encontraron empleados",
      emptyTable: "No hay datos disponibles en la tabla",
      paginate: {
        first: "Primero",
        previous: "Anterior",
        next: "Siguiente",
        last: "Último",
      },
    },
    pageLength: 25,
    scrollY: '500px',
    scrollCollapse: true,
    paging: false,
    responsive: true,
    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"mb-2 sm:mb-0"l><"mb-2 sm:mb-0"f>>rtip',
    drawCallback: () => {
      $(".dataTables_wrapper").addClass("overflow-x-auto")
    },
  })
}

function applyFilters() {
  const form = document.getElementById("filterForm")
  const formData = new FormData(form)
  const filters = {}

  for (const [key, value] of formData.entries()) {
    if (value.trim()) {
      filters[key] = value
    }
  }

  const params = new URLSearchParams(filters)
  const newUrl = `/api/dashboard/employees?${params}`

  if (employeesDataTable) {
    employeesDataTable.ajax.url(newUrl).load()
  }
}

function clearFilters() {
  document.getElementById("filterForm").reset()

  if (employeesDataTable) {
    employeesDataTable.ajax.url("/api/dashboard/employees").load()
  }
}

async function loadDashboardData() {
  try {
    await Promise.all([
      loadGenderDistribution(),
      loadAgeDistribution(),
      loadCityDistribution(),
      loadEducationDistribution(),
      loadExperiencePayCorrelation(),
      loadBenchedHistory(),
      loadLeavePrediction(),
    ])
  } catch (error) {
    console.error("Error loading dashboard data:", error)
    alert("Error al cargar los datos del dashboard")
  }
}

async function loadGenderDistribution() {
  try {
    const response = await axios.get("/dashboard/gender-distribution", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const ctx = document.getElementById("genderChart").getContext("2d")

    if (charts.gender) {
      charts.gender.destroy()
    }

    charts.gender = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: data.map((item) => item.gender),
        datasets: [
          {
            data: data.map((item) => item.count),
            backgroundColor: ["#3B82F6", "#EF4444", "#10B981"],
            borderWidth: 2,
            borderColor: "#fff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading gender distribution:", error)
  }
}

async function loadAgeDistribution() {
  try {
    const response = await axios.get("/dashboard/age-distribution", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const ctx = document.getElementById("ageChart").getContext("2d")

    if (charts.age) {
      charts.age.destroy()
    }

    charts.age = new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.map((item) => item.age_range),
        datasets: [
          {
            label: "Empleados",
            data: data.map((item) => item.count),
            backgroundColor: "#F97316",
            borderColor: "#EA580C",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading age distribution:", error)
  }
}

async function loadCityDistribution() {
  try {
    const response = await axios.get("/dashboard/city-distribution", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const ctx = document.getElementById("cityChart").getContext("2d")

    if (charts.city) {
      charts.city.destroy()
    }

    charts.city = new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.map((item) => item.city),
        datasets: [
          {
            label: "Empleados",
            data: data.map((item) => item.count),
            backgroundColor: "#3B82F6",
            borderColor: "#2563EB",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: "y",
        scales: {
          x: {
            beginAtZero: true,
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading city distribution:", error)
  }
}

async function loadEducationDistribution() {
  try {
    const response = await axios.get("/dashboard/education-distribution", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const ctx = document.getElementById("educationChart").getContext("2d")

    if (charts.education) {
      charts.education.destroy()
    }

    charts.education = new Chart(ctx, {
      type: "pie",
      data: {
        labels: data.map((item) => item.education),
        datasets: [
          {
            data: data.map((item) => item.count),
            backgroundColor: ["#8B5CF6", "#06B6D4", "#10B981", "#F59E0B", "#EF4444"],
            borderWidth: 2,
            borderColor: "#fff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading education distribution:", error)
  }
}

async function loadExperiencePayCorrelation() {
  try {
    const response = await axios.get("/dashboard/experience-pay-correlation", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data
    const experienceRanges = response.data.experience_ranges

    const ctx = document.getElementById("experiencePayChart").getContext("2d")

    if (charts.experiencePay) {
      charts.experiencePay.destroy()
    }

    const colors = ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6"]
    const datasets = []

    Object.keys(data).forEach((tier, index) => {
      datasets.push({
        label: tier,
        data: experienceRanges.map((range) => data[tier][range] || 0),
        backgroundColor: colors[index % colors.length],
        borderColor: colors[index % colors.length],
        borderWidth: 1,
      })
    })

    charts.experiencePay = new Chart(ctx, {
      type: "bar",
      data: {
        labels: experienceRanges,
        datasets: datasets,
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            title: {
              display: true,
              text: "Rangos de Experiencia",
            },
          },
          y: {
            title: {
              display: true,
              text: "Número de Empleados",
            },
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              callback: (value) => Math.floor(value),
            },
          },
        },
        plugins: {
          legend: {
            position: "top",
          },
          tooltip: {
            mode: "index",
            intersect: false,
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading experience-pay correlation:", error)
  }
}

async function loadBenchedHistory() {
  try {
    const response = await axios.get("/dashboard/benched-history", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const ctx = document.getElementById("benchedChart").getContext("2d")

    if (charts.benched) {
      charts.benched.destroy()
    }

    charts.benched = new Chart(ctx, {
      type: "pie",
      data: {
        labels: data.map((item) => item.benched_status),
        datasets: [
          {
            data: data.map((item) => item.count),
            backgroundColor: ["#EF4444", "#10B981"],
            borderWidth: 2,
            borderColor: "#fff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                const total = context.dataset.data.reduce((a, b) => a + b, 0)
                const percentage = ((context.parsed * 100) / total).toFixed(1)
                return `${context.label}: ${context.parsed} (${percentage}%)`
              },
            },
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading benched history:", error)
  }
}

async function loadLeavePrediction() {
  try {
    const response = await axios.get("/dashboard/leave-prediction", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
      },
    })
    const data = response.data.data

    const ctx = document.getElementById("leaveChart").getContext("2d")

    if (charts.leave) {
      charts.leave.destroy()
    }

    charts.leave = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: data.map((item) => item.status),
        datasets: [
          {
            data: data.map((item) => item.count),
            backgroundColor: ["#10B981","#EF4444"],
            borderWidth: 2,
            borderColor: "#fff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    })
  } catch (error) {
    console.error("Error loading leave prediction:", error)
  }
}

function refreshDashboard() {
  loadDashboardData()
}

function logout() {
  if (confirm("¿Estás seguro de que quieres cerrar sesión?")) {
    localStorage.removeItem("jwt_token")
    localStorage.removeItem("user_data")
    window.location.href = "/login"
  }
}

function openAddEmployeeModal() {
  document.getElementById("addEmployeeModal").classList.remove("hidden")
}

function closeAddEmployeeModal() {
  document.getElementById("addEmployeeModal").classList.add("hidden")
  document.getElementById("addEmployeeForm").reset()
}
