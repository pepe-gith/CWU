const daysTag = document.querySelector(".days"),
currentDate = document.querySelector(".current-date"),
prevNextIcon = document.querySelectorAll(".icons span");


// toma nueva fecha, año y mes actual
let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

// almacena los nombres de todos los meses en el array months
const months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
              "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

const renderCalendar = () => {
    let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(), // toma el 1º día del mes
    lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(), // toma la última fecha del mes
    lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(), // toma el último día del mes
    lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate(); // toma la última fecha del mes anterior
    let liTag = "";

    for (let i = firstDayofMonth; i > 0; i--) { // crea lista de últimos días mes anterior
        liTag += `<li class="inactive daycell">${lastDateofLastMonth - i + 1}</li>`;
    }

    for (let i = 1; i <= lastDateofMonth; i++) { // crea lista de los días del mes actual
        // añade la clase active en la lista en el día mes y año 
        let isToday = i === date.getDate() && currMonth === new Date().getMonth() 
                     && currYear === new Date().getFullYear() ? "active" : "";
        liTag += `<li class="${isToday}" id="day${i}" onclick="getDate(${i})">${i}</li>`;
        /*if ((i==23) || (i==26) || (i==27)|| (i==29) || (i==30)) {
             liTag += `<li class="disabled" id="day${i}">${i}</li>`;
        }
        else liTag += `<li class="${isToday}" id="day${i}" onclick="getDate(${i})">${i}</li>`;
        */
    }

    for (let i = lastDayofMonth; i < 6; i++) { // crea lista de los primeros días del mes próximo
        liTag += `<li class="inactive daycell">${i - lastDayofMonth + 1}</li>`
    }
    currentDate.innerText = `${months[currMonth]} ${currYear}`; // transforma a texto mes y año actual
    daysTag.innerHTML = liTag;

}
renderCalendar();
disableDates();

prevNextIcon.forEach(icon => { // mes previo y mes anterior
    icon.addEventListener("click", () => { // añade el evento click a ambos iconos
        // si clicas el icono de mes anterior se decrementa el mes actual, si no se incrementa
        currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;
        // si mes actual es menor que 0 o mayor que 11
        if(currMonth < 0 || currMonth > 11) { 
            // se crea nueva fecha de año y mes y se pasa como tipo fecha
            date = new Date(currYear, currMonth, new Date().getDate());
            currYear = date.getFullYear(); // actualiza año 
            currMonth = date.getMonth(); // actualiza mes
        } else {
            date = new Date(); // pasa el fecha actual como tipo fecha
        }
        renderCalendar(); // ejecuta renderCalendar
        disableDates();  // ejecuta disableDates
    });
});

function getDate(day) {
    console.log(currYear);
    let dR = new Date(currYear, currMonth, day);
    console.log(dR);
    alert(dR);
}

async function disableDates() {
    
    `<li#day5 class="inactive daycell">`;
    `li#day5.null`;
    let reservedDatesArr = [5, 10, 28, 31]
    alert("entra en disableDates");
    /*reservedDatesArr.forEach(date => {
        $(`li#day${date}`).addClass("disabled");
        $(`li#day${date}`).removeAttr("onclick", null);
        
    });*/
}

