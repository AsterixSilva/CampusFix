import './bootstrap';

const initializeTechnicianTeamField = () => {
    const form = document.querySelector('[data-admin-user-form]');
    const roleSelect = form?.querySelector('[data-admin-user-role]');
    const teamSection = form?.querySelector('[data-technician-team-section]');
    const teamSelect = teamSection?.querySelector('select');

    if (!roleSelect || !teamSection) return;

    const updateVisibility = () => {
        const isTechnician = roleSelect.value === 'technician';

        teamSection.hidden = !isTechnician;

        if (teamSelect) {
            teamSelect.disabled = !isTechnician;
            teamSelect.required = isTechnician;
        }
    };

    roleSelect.addEventListener('change', updateVisibility);
    updateVisibility();
};

const initializeCampusClock = () => {
    const clock = document.querySelector('[data-campus-clock]');
    const date = document.querySelector('[data-campus-date]');
    const greeting = document.querySelector('[data-campus-greeting]');

    if (!clock && !date && !greeting) return;

    const timeZone = clock?.dataset.timezone || date?.dataset.timezone || greeting?.dataset.timezone || 'Asia/Jakarta';
    const dateFormatter = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone,
    });
    const clockFormatter = new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hourCycle: 'h23',
        timeZone,
    });
    const hourFormatter = new Intl.DateTimeFormat('en-US', {
        hour: '2-digit',
        hourCycle: 'h23',
        timeZone,
    });

    const updateCampusTime = () => {
        const now = new Date();
        const hour = Number(hourFormatter.format(now));

        if (clock) {
            clock.textContent = clockFormatter.format(now);
            clock.dateTime = now.toISOString();
        }

        if (date) {
            date.textContent = dateFormatter.format(now);
            date.dateTime = now.toISOString();
        }

        if (greeting) {
            greeting.textContent = hour < 11
                ? 'Selamat pagi'
                : hour < 15
                    ? 'Selamat siang'
                    : hour < 18
                        ? 'Selamat sore'
                        : 'Selamat malam';
        }
    };

    updateCampusTime();
    window.setInterval(updateCampusTime, 1000);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeTechnicianTeamField();
        initializeCampusClock();
    }, { once: true });
} else {
    initializeTechnicianTeamField();
    initializeCampusClock();
}
