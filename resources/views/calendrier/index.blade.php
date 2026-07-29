@extends('layouts.main')
@section('title', 'Calendrier')
@section('breadcrumb', 'Calendrier')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">📅 Calendrier des réservations</div>
        <div class="page-subtitle">Visualisez les journées disponibles et réservées</div>
    </div>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary">+ Nouvelle réservation</a>
</div>

<div class="panel">
    <div class="panel-body">
        <div id="calendar"></div>
        <div style="display:flex;gap:20px;margin-top:16px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:8px;font-size:13px">
                <div style="width:14px;height:14px;background:#fed7aa;
                            border:1px solid #fdba74;border-radius:3px"></div>
                Partiellement réservé
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px">
                <div style="width:14px;height:14px;background:#fee2e2;
                            border:1px solid #fca5a5;border-radius:3px"></div>
                Complet (5 créneaux)
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px">
                <div style="width:14px;height:14px;background:#dcfce7;
                            border:1px solid #bbf7d0;border-radius:3px"></div>
                Confirmé
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px">
                <div style="width:14px;height:14px;background:#eff6ff;
                            border:1px solid #2563eb;border-radius:3px"></div>
                Aujourd'hui
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Données des réservations depuis Laravel
    const reservations = @json($reservations);

    let currentDate = new Date();

    function renderCalendar() {
        const year  = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const today = new Date();

        const firstDay = new Date(year, month, 1);
        const lastDay  = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;

        const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin',
                           'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
        const days = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

        let html = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                <button onclick="prevMonth()" class="btn btn-outline btn-sm">‹ Préc</button>
                <h2 style="font-size:20px;font-weight:800">
                    ${monthNames[month]} ${year}
                </h2>
                <div style="display:flex;gap:8px">
                    <button onclick="nextMonth()" class="btn btn-outline btn-sm">Suiv ›</button>
                    <button onclick="goToday()" class="btn btn-primary btn-sm">Aujourd'hui</button>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px">
        `;

        // Jours de la semaine
        days.forEach(d => {
            html += `<div style="text-align:center;font-size:11px;font-weight:700;
                                 color:#64748b;text-transform:uppercase;padding:8px 4px">
                        ${d}
                    </div>`;
        });

        // Cellules vides début
        for (let i = 0; i < startDay; i++) {
            const d = new Date(year, month, -startDay + i + 1);
            html += `<div style="border:1px solid #e2e8f0;border-radius:8px;
                                 min-height:90px;padding:6px;background:#f8fafc">
                        <div style="color:#cbd5e1;font-size:13px;font-weight:700">
                            ${d.getDate()}
                        </div>
                    </div>`;
        }

        // Jours du mois
        for (let d = 1; d <= lastDay.getDate(); d++) {
            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const evs     = reservations[dateStr] || [];
            const isToday = d === today.getDate() &&
                            month === today.getMonth() &&
                            year === today.getFullYear();

            let bg = '#fff', border = '#e2e8f0';
            if (isToday)        { bg = '#eff6ff'; border = '#2563eb'; }
            else if (evs.length >= 5) { bg = '#fee2e2'; border = '#fca5a5'; }
            else if (evs.length > 0)  { bg = '#fff7ed'; border = '#fdba74'; }

            html += `<div style="border:1px solid ${border};border-radius:8px;
                                 min-height:90px;padding:6px;background:${bg};
                                 cursor:pointer;transition:all 0.15s"
                         onclick="window.location='{{ route('reservations.create') }}?date=${dateStr}'"
                         onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.boxShadow='none'">
                        <div style="font-size:13px;font-weight:700;color:#0f172a">${d}</div>`;

            evs.slice(0, 2).forEach(ev => {
                const color = ev.statut === 'confirme' ? '#dcfce7;color:#15803d' : '#fed7aa;color:#c2410c';
                html += `<div style="font-size:10px;font-weight:600;padding:2px 5px;
                                     border-radius:4px;margin-top:3px;
                                     background:${color};
                                     white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            ${ev.equipeA.nom} vs ${ev.equipeB.nom}
                        </div>`;
            });

            if (evs.length > 2) {
                html += `<div style="font-size:10px;font-weight:600;padding:2px 5px;
                                     border-radius:4px;margin-top:3px;
                                     background:#dbeafe;color:#1e40af">
                            +${evs.length - 2} autres
                        </div>`;
            }

            html += `</div>`;
        }

        // Cellules vides fin
        const totalCells = startDay + lastDay.getDate();
        const endPad = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= endPad; i++) {
            html += `<div style="border:1px solid #e2e8f0;border-radius:8px;
                                 min-height:90px;padding:6px;background:#f8fafc">
                        <div style="color:#cbd5e1;font-size:13px;font-weight:700">${i}</div>
                    </div>`;
        }

        html += '</div>';
        document.getElementById('calendar').innerHTML = html;
    }

    function prevMonth() {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        renderCalendar();
    }
    function nextMonth() {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
        renderCalendar();
    }
    function goToday() {
        currentDate = new Date();
        renderCalendar();
    }

    renderCalendar();
</script>
@endsection
