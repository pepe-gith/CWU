import './solEvento.css'

const cal = document.getElementById('cal')
if (cal) {
    flatpickr(cal, {
        locale: 'es',
        minDate: 'today',
        dateFormat: 'Y-m-d',
        inline: true,
        disable: [],
        onChange: function(_selectedDates, dateStr) {
            document.getElementById('fecha_evento').value = dateStr
        }
    })
}
