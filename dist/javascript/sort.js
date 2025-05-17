document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('appointmentsTable');
    const tbody = table.querySelector('tbody');
    let rows = Array.from(tbody.querySelectorAll('tr'));
  
    // Filter out rows where data-status is "cancelled"
    rows = rows.filter(row => {
      const status = row.getAttribute('data-status');
      return status && status.toLowerCase() !== 'cancelled';
    });
  
    // Sort rows by the numeric value extracted from the "Time" cell (index 6)
    rows.sort((a, b) => {
      const timeA = a.cells[6].textContent.replace(' mins', '').trim();
      const timeB = b.cells[6].textContent.replace(' mins', '').trim();
      const numA = parseInt(timeA) || 0;
      const numB = parseInt(timeB) || 0;
      return numA - numB;
    });
  
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
  });
  
