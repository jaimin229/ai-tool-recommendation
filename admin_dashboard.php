<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<style>
    .admin-table-container {
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        background: rgba(15, 15, 25, 0.4);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .table-dark {
        --bs-table-bg: transparent;
        --bs-table-striped-bg: rgba(255, 255, 255, 0.02);
        --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
        color: #f8fafc;
        margin-bottom: 0;
    }
    
    .table-dark th {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        color: #94a3b8;
        padding: 1.5rem 1rem;
    }
    
    .table-dark td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .btn-edit {
        background: rgba(59, 130, 246, 0.1);
        color: #60a5fa;
    }
    
    .btn-edit:hover {
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
    }
    
    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
    }
    
    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }

    /* Floating Modal Deep Glassmorphism */
    .modal-content.spatial-modal {
        background: rgba(20, 20, 30, 0.75);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6), 0 0 20px rgba(255, 255, 255, 0.02) inset;
        color: #fff;
    }
    
    .spatial-modal .modal-header {
        border-bottom: none;
        padding: 1.5rem 1.5rem 0.5rem;
    }
    
    .spatial-modal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: 0.5;
    }
    
    .spatial-modal .btn-close:hover {
        opacity: 1;
    }
    
    .spatial-modal .form-control, .spatial-modal .form-select {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .spatial-modal .form-control:focus, .spatial-modal .form-select:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: #8b5cf6;
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.3);
    }
    
    .spatial-modal .form-control::placeholder {
        color: rgba(255, 255, 255, 0.3);
    }
    
    .spatial-modal option {
        background: #1e1b4b;
        color: white;
    }
</style>

<div class="container-fluid px-4 mt-5 pt-4 mb-5" style="min-height: 70vh;">
    <!-- Dashboard Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1">Admin Control Panel</h2>
            <p class="text-secondary small">Manage AI tools, users, and platform settings.</p>
        </div>
        <button class="btn btn-glow rounded-pill px-4 py-2 mt-3 mt-md-0 fw-semibold" data-bs-toggle="modal" data-bs-target="#addToolModal">+ Add New Tool</button>
    </div>
    
    <!-- Data Table Container -->
    <div class="admin-table-container">
        <div class="table-responsive">
            <table class="table table-dark table-hover table-borderless align-middle">
                <thead>
                    <tr>
                        <th scope="col" class="ps-4">ID</th>
                        <th scope="col">Tool Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Pricing</th>
                        <th scope="col" class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 text-secondary">#1024</td>
                        <td class="fw-medium text-white">DevMind AI</td>
                        <td><span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 rounded-pill px-3">Code Assistant</span></td>
                        <td>Freemium</td>
                        <td class="text-end pe-4">
                            <button class="btn-icon btn-edit me-2" title="Edit">✎</button>
                            <button class="btn-icon btn-delete" title="Delete">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-secondary">#1025</td>
                        <td class="fw-medium text-white">Visionary Studio</td>
                        <td><span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 rounded-pill px-3">Image Gen</span></td>
                        <td>Paid</td>
                        <td class="text-end pe-4">
                            <button class="btn-icon btn-edit me-2" title="Edit">✎</button>
                            <button class="btn-icon btn-delete" title="Delete">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-secondary">#1026</td>
                        <td class="fw-medium text-white">DataSense Pro</td>
                        <td><span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 rounded-pill px-3">Data Analysis</span></td>
                        <td>Free</td>
                        <td class="text-end pe-4">
                            <button class="btn-icon btn-edit me-2" title="Edit">✎</button>
                            <button class="btn-icon btn-delete" title="Delete">🗑</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add New Tool Modal -->
<div class="modal fade" id="addToolModal" tabindex="-1" aria-labelledby="addToolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content spatial-modal">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white" id="addToolModalLabel">Add AI Tool</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form>
                    <div class="mb-3">
                        <label for="toolName" class="form-label text-light small fw-medium">Tool Name</label>
                        <input type="text" class="form-control rounded-3" id="toolName" placeholder="Enter tool name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="toolUrl" class="form-label text-light small fw-medium">Website URL</label>
                        <input type="url" class="form-control rounded-3" id="toolUrl" placeholder="https://...">
                    </div>
                    
                    <div class="mb-3">
                        <label for="toolCategory" class="form-label text-light small fw-medium">Category</label>
                        <select class="form-select rounded-3" id="toolCategory">
                            <option selected disabled>Select a category</option>
                            <option value="1">Code Assistant</option>
                            <option value="2">Image Generation</option>
                            <option value="3">Data Analysis</option>
                            <option value="4">Writing/Content</option>
                            <option value="5">Audio/Video</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-light small fw-medium d-block">Pricing Model</label>
                        <div class="form-check form-check-inline mt-1">
                            <input class="form-check-input" type="radio" name="pricingOptions" id="priceFree" value="free">
                            <label class="form-check-label text-secondary small" for="priceFree">Free</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pricingOptions" id="priceFreemium" value="freemium" checked>
                            <label class="form-check-label text-secondary small" for="priceFreemium">Freemium</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pricingOptions" id="pricePaid" value="paid">
                            <label class="form-check-label text-secondary small" for="pricePaid">Paid</label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="toolDesc" class="form-label text-light small fw-medium">Description</label>
                        <textarea class="form-control rounded-3" id="toolDesc" rows="3" placeholder="Brief description of what the tool does..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-glow w-100 py-2 rounded-pill fw-semibold">Save Tool</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
