<style>
/* ════════════════════════════════════════════════════════════════
   KaziTrust — Theme Filament v3
   Règle : styles dark sous .dark { }  /  styles light sans préfixe
════════════════════════════════════════════════════════════════ */

@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

/* ── Font (les deux modes) ───────────────────────────────────── */
body, .fi-body {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

/* ══════════════════════════════════════════════════════════════
   MODE CLAIR  (pas de préfixe .dark)
══════════════════════════════════════════════════════════════ */

.fi-body, .fi-main, .fi-main-ctn    { background-color: #F8FAFC !important; }
.fi-sidebar                          { background-color: #FFFFFF !important; border-right: 1px solid #E2E8F0 !important; }
.fi-sidebar-header                   { background-color: #FFFFFF !important; border-bottom: 1px solid #F1F5F9 !important; }
.fi-topbar                           { background-color: #FFFFFF !important; border-bottom: 1px solid #E2E8F0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important; }

.fi-sidebar-item-button              { border-radius: 9px !important; color: #64748B !important; transition: all 0.18s !important; }
.fi-sidebar-item-button:hover        { background: #F1F5F9 !important; color: #1E293B !important; }
.fi-sidebar-item-button.fi-active,
.fi-sidebar-item-button[aria-current]{ background: #EFF6FF !important; color: #2563EB !important; border-left: 2px solid #2563EB !important; }
.fi-sidebar-item-label               { font-size: 0.82rem !important; font-weight: 600 !important; }
.fi-sidebar-group-label              { color: #94A3B8 !important; font-size: 0.68rem !important; font-weight: 700 !important; letter-spacing: 0.12em !important; text-transform: uppercase !important; }

.fi-page-header-heading   { color: #0F172A !important; font-size: 1.5rem !important; font-weight: 800 !important; letter-spacing: -0.04em !important; }
.fi-page-header-subheading{ color: #64748B !important; font-size: 0.85rem !important; }

.fi-section               { background: #FFFFFF !important; border: 1px solid #E2E8F0 !important; border-radius: 14px !important; box-shadow: 0 1px 4px rgba(0,0,0,0.05) !important; }
.fi-section-header        { border-bottom: 1px solid #F1F5F9 !important; }
.fi-section-header-heading{ color: #0F172A !important; font-weight: 700 !important; }

.fi-wi-stats-overview-stat {
    background: #FFFFFF !important; border: 1px solid #E2E8F0 !important;
    border-radius: 14px !important; padding: 1.4rem 1.5rem !important;
    transition: transform 0.2s, box-shadow 0.2s !important;
    position: relative !important; overflow: hidden !important;
}
.fi-wi-stats-overview-stat::before {
    content:''; position:absolute; top:0; left:0; right:0; height:2px;
    background: linear-gradient(90deg, #2563EB, transparent); opacity:0.5;
}
.fi-wi-stats-overview-stat:hover    { transform: translateY(-2px) !important; box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important; }
.fi-wi-stats-overview-stat-label    { color: #64748B !important; font-size: 0.75rem !important; font-weight: 600 !important; letter-spacing: 0.08em !important; text-transform: uppercase !important; }
.fi-wi-stats-overview-stat-value    { color: #0F172A !important; font-size: 2rem !important; font-weight: 800 !important; letter-spacing: -0.04em !important; }
.fi-wi-stats-overview-stat-description { color: #64748B !important; font-size: 0.78rem !important; }

.fi-ta-ctn               { background: #FFFFFF !important; border: 1px solid #E2E8F0 !important; border-radius: 14px !important; overflow: hidden !important; }
.fi-ta-header-cell-label { color: #64748B !important; font-size: 0.72rem !important; font-weight: 700 !important; letter-spacing: 0.08em !important; text-transform: uppercase !important; }
.fi-ta-header, .fi-ta-header-cell { background: #F8FAFC !important; border-bottom: 1px solid #E2E8F0 !important; }
.fi-ta-row               { border-bottom: 1px solid #F1F5F9 !important; transition: background 0.15s !important; }
.fi-ta-row:hover         { background: #F8FAFC !important; }
.fi-ta-cell              { color: #334155 !important; font-size: 0.85rem !important; }

.fi-input, .fi-input-wrp input, .fi-select-input, .fi-textarea {
    background: #FFFFFF !important; border-color: #E2E8F0 !important; color: #1E293B !important; border-radius: 9px !important;
}
.fi-input:focus, .fi-input-wrp input:focus { border-color: #2563EB !important; box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important; }
.fi-label, label { color: #374151 !important; font-size: 0.82rem !important; font-weight: 600 !important; }

.fi-btn-primary, .fi-btn[data-fi-color="primary"] { background: #2563EB !important; border-color: #2563EB !important; border-radius: 9px !important; font-weight: 700 !important; box-shadow: 0 1px 3px rgba(37,99,235,0.3) !important; }
.fi-btn-primary:hover   { background: #1D4ED8 !important; box-shadow: 0 4px 14px rgba(37,99,235,0.3) !important; transform: translateY(-1px) !important; }
.fi-btn-color-gray      { background: #F1F5F9 !important; border: 1px solid #E2E8F0 !important; color: #374151 !important; border-radius: 9px !important; }

.fi-badge { border-radius: 6px !important; font-size: 0.7rem !important; font-weight: 700 !important; }
.fi-badge-color-success { background: #D1FAE5 !important; color: #059669 !important; border: 1px solid #A7F3D0 !important; }
.fi-badge-color-danger  { background: #FEE2E2 !important; color: #DC2626 !important; border: 1px solid #FECACA !important; }
.fi-badge-color-warning { background: #FEF3C7 !important; color: #D97706 !important; border: 1px solid #FDE68A !important; }
.fi-badge-color-primary,
.fi-badge-color-info    { background: #DBEAFE !important; color: #2563EB !important; border: 1px solid #BFDBFE !important; }

.fi-dropdown-panel  { background: #FFFFFF !important; border: 1px solid #E2E8F0 !important; border-radius: 12px !important; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }
.fi-dropdown-item   { color: #374151 !important; border-radius: 8px !important; font-size: 0.85rem !important; }
.fi-dropdown-item:hover { background: #F1F5F9 !important; color: #0F172A !important; }
.fi-modal-window    { background: #FFFFFF !important; border: 1px solid #E2E8F0 !important; border-radius: 16px !important; }
.fi-modal-header    { border-bottom: 1px solid #F1F5F9 !important; }
.fi-modal-header-heading { color: #0F172A !important; font-weight: 700 !important; }

.fi-pagination-item-btn       { background: #FFFFFF !important; border-color: #E2E8F0 !important; color: #64748B !important; border-radius: 7px !important; }
.fi-pagination-item-btn:hover { background: #EFF6FF !important; color: #2563EB !important; }

::-webkit-scrollbar       { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: #F1F5F9; }
::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

/* ══════════════════════════════════════════════════════════════
   MODE SOMBRE  (tout sous .dark)
══════════════════════════════════════════════════════════════ */

.dark .fi-body, .dark .fi-main, .dark .fi-main-ctn { background-color: #080E1A !important; }
.dark .fi-sidebar        { background-color: #0F172A !important; border-right: 1px solid rgba(255,255,255,0.05) !important; }
.dark .fi-sidebar-header { background-color: #0F172A !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
.dark .fi-topbar         { background-color: #0F172A !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; box-shadow: none !important; }

.dark .fi-sidebar-item-button              { color: #64748B !important; }
.dark .fi-sidebar-item-button:hover        { background: rgba(37,99,235,0.08) !important; color: #94A3B8 !important; }
.dark .fi-sidebar-item-button.fi-active,
.dark .fi-sidebar-item-button[aria-current]{ background: rgba(37,99,235,0.15) !important; color: #60A5FA !important; border-left: 2px solid #2563EB !important; }
.dark .fi-sidebar-group-label              { color: #334155 !important; }

.dark .fi-page-header-heading   { color: #F1F5F9 !important; }
.dark .fi-page-header-subheading{ color: #475569 !important; }

.dark .fi-section               { background: #1E293B !important; border: 1px solid rgba(255,255,255,0.05) !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }
.dark .fi-section-header        { border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
.dark .fi-section-header-heading{ color: #E2E8F0 !important; }

.dark .fi-wi-stats-overview-stat       { background: #1E293B !important; border: 1px solid rgba(255,255,255,0.05) !important; }
.dark .fi-wi-stats-overview-stat:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.25), 0 0 0 1px rgba(37,99,235,0.15) !important; }
.dark .fi-wi-stats-overview-stat-label { color: #64748B !important; }
.dark .fi-wi-stats-overview-stat-value { color: #F1F5F9 !important; }
.dark .fi-wi-stats-overview-stat-description { color: #475569 !important; }

.dark .fi-ta-ctn               { background: #1E293B !important; border: 1px solid rgba(255,255,255,0.05) !important; }
.dark .fi-ta-header-cell-label { color: #475569 !important; }
.dark .fi-ta-header, .dark .fi-ta-header-cell { background: rgba(15,23,42,0.5) !important; border-bottom: 1px solid rgba(255,255,255,0.04) !important; }
.dark .fi-ta-row               { border-bottom: 1px solid rgba(255,255,255,0.03) !important; }
.dark .fi-ta-row:hover         { background: rgba(37,99,235,0.04) !important; }
.dark .fi-ta-cell              { color: #94A3B8 !important; }

.dark .fi-input, .dark .fi-input-wrp input, .dark .fi-select-input, .dark .fi-textarea {
    background: #0F172A !important; border-color: rgba(148,163,184,0.1) !important; color: #E2E8F0 !important;
}
.dark .fi-input:focus, .dark .fi-input-wrp input:focus { border-color: rgba(37,99,235,0.5) !important; box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important; }
.dark .fi-label, .dark label { color: #64748B !important; }

.dark .fi-btn-primary, .dark .fi-btn[data-fi-color="primary"] { background: #2563EB !important; border-color: #2563EB !important; box-shadow: 0 0 16px rgba(37,99,235,0.25) !important; }
.dark .fi-btn-primary:hover  { background: #1D4ED8 !important; box-shadow: 0 0 28px rgba(37,99,235,0.4) !important; }
.dark .fi-btn-color-gray     { background: rgba(30,41,59,0.8) !important; border: 1px solid rgba(255,255,255,0.07) !important; color: #94A3B8 !important; }

.dark .fi-badge-color-success { background: rgba(52,211,153,0.12)  !important; color: #34D399 !important; border: 1px solid rgba(52,211,153,0.2)  !important; }
.dark .fi-badge-color-danger  { background: rgba(248,113,113,0.12) !important; color: #F87171 !important; border: 1px solid rgba(248,113,113,0.2) !important; }
.dark .fi-badge-color-warning { background: rgba(251,191,36,0.12)  !important; color: #FBBF24 !important; border: 1px solid rgba(251,191,36,0.2)  !important; }
.dark .fi-badge-color-primary,
.dark .fi-badge-color-info    { background: rgba(37,99,235,0.12)   !important; color: #60A5FA !important; border: 1px solid rgba(37,99,235,0.2)   !important; }

.dark .fi-dropdown-panel  { background: #1E293B !important; border: 1px solid rgba(255,255,255,0.07) !important; box-shadow: 0 20px 40px rgba(0,0,0,0.5) !important; }
.dark .fi-dropdown-item   { color: #94A3B8 !important; }
.dark .fi-dropdown-item:hover { background: rgba(37,99,235,0.08) !important; color: #E2E8F0 !important; }
.dark .fi-modal-window    { background: #1E293B !important; border: 1px solid rgba(255,255,255,0.07) !important; }
.dark .fi-modal-header    { border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
.dark .fi-modal-header-heading { color: #F1F5F9 !important; }

.dark .fi-pagination-item-btn       { background: #1E293B !important; border-color: rgba(255,255,255,0.07) !important; color: #64748B !important; }
.dark .fi-pagination-item-btn:hover { background: rgba(37,99,235,0.12) !important; color: #60A5FA !important; }

.dark ::-webkit-scrollbar-track { background: #0F172A; }
.dark ::-webkit-scrollbar-thumb { background: #1E293B; }
.dark ::-webkit-scrollbar-thumb:hover { background: #334155; }
</style>