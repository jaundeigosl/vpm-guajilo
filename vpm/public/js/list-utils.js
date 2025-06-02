document.addEventListener('DOMContentLoaded', () => {
    // Configuración de TableExport
    const options = {
        formats: ['xlsx', 'csv', 'txt'],
        filename: 'proveedores',
        sheetname: 'Proveedores'
    };

    // Inicializar TableExport
    const tableExport = new TableExport(document.getElementById('providers-table'), options);

    // Botones de exportación
    document.getElementById('copy-button').addEventListener('click', () => {
        tableExport.export('txt');
    });

    document.getElementById('excel-button').addEventListener('click', () => {
        tableExport.export('xlsx');
    });

    document.getElementById('pdf-button').addEventListener('click', () => {
        tableExport.export('pdf');
    });
});

document.getElementById('columns-select').addEventListener('change', () => {
    const selectedColumns = Array.from(document.getElementById('columns-select').selectedOptions).map(option => option.value);
    const tableHeaders = document.querySelectorAll('#providers-table thead th');
    const tableRows = document.querySelectorAll('#providers-table tbody tr');

    tableHeaders.forEach((header, index) => {
        header.style.display = selectedColumns.includes(header.textContent.trim()) ? '' : 'none';
    });

    tableRows.forEach(row => {
        row.querySelectorAll('td').forEach((cell, index) => {
            cell.style.display = selectedColumns.includes(tableHeaders[index].textContent.trim()) ? '' : 'none';
        });
    });
});
document.getElementById('search-input').addEventListener('input', () => {
    const searchQuery = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('#providers-table tbody tr');

    rows.forEach(row => {
        const cells = Array.from(row.cells).map(cell => cell.textContent.toLowerCase());
        const isMatch = cells.some(cell => cell.includes(searchQuery));
        row.style.display = isMatch ? '' : 'none';
    });
});
