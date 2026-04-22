<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body { background-color: #ffffff; font-family: system-ui, sans-serif; overflow-x: hidden; }
    .text-teal { color: #0d9488; }
    .sidebar { background-color: #f8fafc; border-right: 1px solid #f1f5f9; width: 250px; transition: 0.3s; overflow: hidden; white-space: nowrap; }
    .sidebar.collapsed { width: 80px; }
    .sidebar.collapsed .nav-text, .sidebar.collapsed .brand-text { display: none !important; }
    .nav-link-custom { color: #64748b; padding: 0.8rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; gap: 0.75rem; transition: 0.3s; text-decoration: none; font-weight: 500; }
    .nav-link-custom:hover { background-color: #f1f5f9; color: #0f172a; }
    .nav-link-custom.active { background-color: #f0fdfa; color: #0d9488; font-weight: bold; }
    .toggle-btn { background: transparent; border: none; color: #64748b; cursor: pointer; }
    .serif-italic { font-family: Georgia, serif; font-style: italic; }
</style>