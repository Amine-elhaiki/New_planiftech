{{-- Corrections pour projects/show.blade.php --}}

@push('styles')
<style>
    /* Styles généraux */
    .project-details {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        margin-bottom: 2rem;
    }

    .project-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .project-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .project-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .progress-section {
        margin: 2rem 0;
    }

    .progress-bar-container {
        background: #f3f4f6;
        border-radius: 8px;
        height: 12px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 8px;
        transition: width 0.3s ease;
    }

    .progress-text {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* CORRECTION CSS - Ligne 339 approximative */
    .task-list {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* CORRIGÉ: valeur rgba complète */
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .task-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.2s ease;
    }

    .task-item:hover {
        background-color: #f9fafb;
    }

    .task-item:last-child {
        border-bottom: none;
    }

    .task-info {
        flex: 1;
    }

    .task-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .task-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .task-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .status-todo {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-in-progress {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .status-completed {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .team-section {
        margin-top: 2rem;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .team-member {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
    }

    .team-member:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .member-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin: 0 auto 0.75rem;
        font-size: 1.25rem;
    }

    .member-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .member-role {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .project-header {
            flex-direction: column;
            gap: 1rem;
        }

        .project-title {
            font-size: 1.5rem;
        }

        .team-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
</style>
@endpush
