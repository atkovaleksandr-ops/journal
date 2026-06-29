<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Journal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            font-family: 'Figtree', Arial, system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.14), transparent 28%),
                #0f172a;
            color: #e5e7eb;
            overflow-x: hidden;
        }

        html.app-menu-open,
        body.app-menu-open {
            height: 100%;
            overflow: hidden;
            overscroll-behavior: none;
        }

        body,
        input,
        select,
        textarea,
        button {
            font-family: 'Figtree', Arial, system-ui, sans-serif;
        }

        .journal-layout {
            min-height: 100vh;
            overflow-x: clip;
        }

        .journal-navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            backdrop-filter: blur(16px);
        }

        .journal-navbar-inner {
            width: min(1120px, 100%);
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .journal-logo {
            flex: 0 0 auto;
            color: white;
            font-size: 22px;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: 0;
        }

        .journal-brand-row {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .journal-logo span {
            color: #38bdf8;
        }

        .journal-nav {
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .journal-nav a,
        .journal-logout {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1 !important;
            text-decoration: none;
            font-size: 14px;
            line-height: 1.2;
            padding: 9px 13px;
            border-radius: 10px;
            transition: 0.2s;
            background: rgba(30, 41, 59, 0.55) !important;
            border: 1px solid rgba(148, 163, 184, 0.18) !important;
            cursor: pointer;
            font-family: inherit;
            box-shadow: none !important;
            white-space: nowrap;
        }

        .journal-nav form {
            margin: 0;
        }

        .app-menu-toggle,
        .app-menu-button,
        .app-menu-overlay,
        .app-menu-head {
            display: none;
        }

        .app-menu-toggle {
            position: fixed;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .journal-nav a:hover,
        .journal-logout:hover {
            background: rgba(59, 130, 246, 0.22) !important;
            color: white !important;
            transform: translateY(-1px);
        }

        .journal-logout {
            background: rgba(220, 38, 38, 0.16) !important;
            color: #fecaca !important;
        }

        .journal-logout:hover {
            background: rgba(220, 38, 38, 0.28) !important;
        }

        .journal-user {
            min-width: 0;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #94a3b8;
            font-size: 14px;
        }

        .journal-main {
            width: min(1120px, 100%);
            margin: 0 auto;
            padding: 30px 24px;
        }

        .container {
            width: 100%;
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.28);
            overflow: hidden;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 26px;
        }

        .page-title {
            margin: 0 0 8px;
            color: white;
            font-size: 27px;
            line-height: 1.2;
        }

        .page-subtitle {
            margin: 0;
            color: #94a3b8;
            max-width: 720px;
            line-height: 1.65;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .section-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .panel {
            background: rgba(30, 41, 59, 0.58);
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 16px;
            padding: 20px;
        }

        .form-card {
            margin-top: 18px;
            padding: 20px;
            border-radius: 16px;
            background: rgba(30, 41, 59, 0.58);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .section-head h2 {
            margin: 0 0 6px;
            color: #ffffff;
            font-size: 18px;
            line-height: 1.25;
        }

        .stat-grid,
        .dashboard-grid,
        .form-grid {
            display: grid;
            gap: 14px;
        }

        .stat-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            margin: 20px 0 2px;
        }

        .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .form-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin: 18px 0;
        }

        .group-builder {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(240px, 0.42fr);
            gap: 18px;
            align-items: stretch;
            margin: 18px 0;
        }

        .group-builder-fields {
            display: grid;
            gap: 16px;
            min-width: 0;
        }

        .group-builder-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(150px, 1fr));
            gap: 14px;
        }

        .group-code-card {
            display: grid;
            align-content: center;
            gap: 10px;
            min-height: 188px;
            padding: 20px;
            border-radius: 16px;
            background: linear-gradient(145deg, rgba(14, 165, 233, 0.16), rgba(22, 163, 74, 0.1));
            border: 1px solid rgba(56, 189, 248, 0.28);
        }

        .group-code-kicker {
            color: #bae6fd;
            font-size: 13px;
            font-weight: 800;
        }

        .group-code-value {
            color: #ffffff;
            font-size: 42px;
            line-height: 1;
            letter-spacing: 0;
            overflow-wrap: anywhere;
        }

        .group-code-text {
            margin: 0;
            color: #cbd5e1;
            line-height: 1.5;
        }

        .stat-card,
        .dashboard-card {
            background: rgba(30, 41, 59, 0.66);
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 16px;
        }

        .stat-card {
            padding: 17px 18px;
        }

        .stat-value {
            display: block;
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 7px;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 14px;
        }

        .dashboard-card {
            display: block;
            padding: 20px;
            text-decoration: none;
            color: white;
            transition: 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            border-color: rgba(56, 189, 248, 0.65);
            background: rgba(30, 41, 59, 0.95);
        }

        .dashboard-card h3 {
            margin: 0 0 8px;
            color: white;
            font-size: 20px;
        }

        .dashboard-card p {
            margin: 0;
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.55;
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            margin-top: 14px;
            color: #38bdf8;
            font-size: 14px;
            font-weight: 800;
        }

        .account-history {
            margin-top: 22px;
        }

        .account-history-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .account-history-title h3 {
            margin: 0;
            font-size: 18px;
        }

        .account-history-title .card-link {
            margin-top: 0;
            white-space: nowrap;
        }

        .empty-state {
            margin-top: 18px;
            padding: 22px;
            border-radius: 16px;
            color: #cbd5e1;
            background: rgba(15, 23, 42, 0.54);
            border: 1px dashed rgba(148, 163, 184, 0.28);
        }

        .field {
            min-width: 0;
            display: grid;
            gap: 8px;
        }

        .field label {
            color: #e2e8f0;
            font-weight: 700;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        .field-hint {
            margin: 0;
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.5;
        }

        .password-line {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            min-width: 0;
        }

        .password-line input {
            min-width: 0;
        }

        .btn-compact {
            min-width: 96px;
            padding: 10px 14px;
            min-height: 44px;
        }

        .credential-grid {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .credential-row {
            display: grid;
            grid-template-columns: 120px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            padding: 12px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.38);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .credential-row span,
        .mini-stat-row span {
            color: #94a3b8;
            font-weight: 700;
        }

        .credential-row strong,
        .mini-stat-row strong {
            color: #ffffff;
            overflow-wrap: anywhere;
        }

        .admin-card {
            margin-bottom: 18px;
        }

        .admin-teacher-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
            gap: 16px;
            align-items: start;
            margin-top: 22px;
        }

        .admin-list-panel {
            padding: 18px;
        }

        .admin-list-panel .section-head,
        .admin-form-card .section-head {
            margin-bottom: 16px;
        }

        .admin-subject-list,
        .admin-lesson-list,
        .admin-form-stack {
            display: grid;
            gap: 12px;
        }

        .admin-subject-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 14px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.13);
        }

        .admin-subject-main {
            display: grid;
            gap: 4px;
            min-width: 0;
        }

        .admin-subject-main strong,
        .admin-lesson-item strong {
            color: #ffffff;
            line-height: 1.35;
        }

        .admin-subject-main span,
        .admin-lesson-item span,
        .admin-current-password p {
            margin: 0;
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.45;
        }

        .admin-subject-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .admin-subject-meta .badge {
            white-space: nowrap;
        }

        .admin-count-pill {
            min-height: 30px;
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            color: #dbeafe;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(96, 165, 250, 0.18);
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .admin-lesson-item {
            display: grid;
            grid-template-columns: 94px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            padding: 13px 14px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .admin-lesson-item time {
            color: #bae6fd;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .admin-lesson-item div {
            display: grid;
            gap: 3px;
            min-width: 0;
        }

        .admin-form-card {
            display: grid;
            gap: 0;
            margin-top: 18px;
        }

        .admin-two-column {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin: 0;
        }

        .admin-current-password {
            display: grid;
            grid-template-columns: minmax(180px, 0.5fr) minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            margin-bottom: 16px;
            padding: 14px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.13);
        }

        .admin-current-password span {
            color: #e2e8f0;
            font-weight: 800;
        }

        .admin-current-password .password-line {
            min-width: 0;
        }

        .mini-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
            padding: 12px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.38);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .alert {
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: 14px;
            font-weight: 700;
        }

        .alert-success {
            color: #bbf7d0;
            background: rgba(22, 163, 74, 0.14);
            border: 1px solid rgba(34, 197, 94, 0.28);
        }

        .alert-danger {
            color: #fecaca;
            background: rgba(220, 38, 38, 0.14);
            border: 1px solid rgba(248, 113, 113, 0.26);
        }

        .muted {
            color: #94a3b8;
            line-height: 1.6;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 6px 10px;
            border-radius: 999px;
            color: #bae6fd;
            background: rgba(14, 165, 233, 0.13);
            border: 1px solid rgba(56, 189, 248, 0.22);
            font-weight: 800;
            font-size: 13px;
        }

        .badge-success {
            color: #bbf7d0;
            background: rgba(34, 197, 94, 0.13);
            border-color: rgba(74, 222, 128, 0.28);
        }

        .badge-warning-soft {
            color: #fed7aa;
            background: rgba(249, 115, 22, 0.12);
            border-color: rgba(251, 146, 60, 0.24);
        }

        .compact-stats {
            margin: 0 0 20px;
        }

        .teacher-workspace {
            overflow: visible;
        }

        .workspace-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .workspace-head h2 {
            margin-bottom: 6px;
        }

        .lesson-form-grid {
            grid-template-columns: minmax(180px, 1fr) minmax(150px, 0.7fr) minmax(260px, 1.4fr);
            align-items: end;
        }

        .field-grow {
            min-width: min(100%, 260px);
        }

        .filter-panel {
            display: grid;
            grid-template-columns: minmax(260px, 1.5fr) minmax(160px, 0.8fr) minmax(190px, 0.9fr) auto;
            gap: 14px;
            align-items: end;
            margin: 18px 0 22px;
            padding: 16px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .filter-panel .field-grow {
            grid-column: auto;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            min-width: max-content;
            align-self: end;
        }

        .filter-actions .btn,
        .filter-actions button {
            min-width: 118px;
        }

        .groups-toolbar {
            grid-template-columns: minmax(280px, 1fr) minmax(220px, 0.5fr) auto;
        }

        .subject-lesson-list {
            display: grid;
            gap: 16px;
        }

        .subject-lesson-section {
            padding: 10px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.14);
            transition: 0.2s;
        }

        .subject-lesson-section.is-open {
            background: rgba(15, 23, 42, 0.56);
            border-color: rgba(56, 189, 248, 0.26);
        }

        .subject-lesson-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .subject-toggle {
            width: 100%;
            min-height: 76px;
            padding: 16px 18px;
            border-radius: 14px;
            border: 1px solid rgba(56, 189, 248, 0.18);
            background:
                linear-gradient(135deg, rgba(56, 189, 248, 0.10), rgba(34, 197, 94, 0.05)),
                rgba(30, 41, 59, 0.72);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            text-align: left;
            box-shadow: none;
            color: #ffffff;
            text-decoration: none;
        }

        .subject-toggle:hover {
            transform: translateY(-1px);
            border-color: rgba(56, 189, 248, 0.42);
            background:
                linear-gradient(135deg, rgba(56, 189, 248, 0.16), rgba(34, 197, 94, 0.08)),
                rgba(30, 41, 59, 0.88);
        }

        .subject-toggle h3 {
            margin: 0 0 5px;
            font-size: 22px;
            line-height: 1.2;
        }

        .subject-toggle p {
            margin: 0;
            color: #93c5fd;
            font-size: 15px;
            font-weight: 600;
        }

        .subject-toggle-meta {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
        }

        .subject-toggle-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            color: #dbeafe;
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.18);
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            transition: 0.2s;
        }

        .subject-toggle[aria-expanded="true"] .subject-toggle-icon {
            transform: rotate(45deg);
            color: #bbf7d0;
            border-color: rgba(74, 222, 128, 0.28);
        }

        .subject-lessons {
            margin-top: 12px;
        }

        .subject-link-button {
            margin: 0;
        }

        .subject-link-button .subject-toggle-icon {
            font-size: 20px;
        }

        .subject-static-head {
            cursor: default;
        }

        .subject-static-head:hover {
            transform: none;
            filter: none;
        }

        .selected-subject-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
            padding: 18px;
            border-radius: 16px;
            background:
                linear-gradient(135deg, rgba(56, 189, 248, 0.12), rgba(34, 197, 94, 0.06)),
                rgba(15, 23, 42, 0.46);
            border: 1px solid rgba(56, 189, 248, 0.18);
        }

        .selected-subject-head h2 {
            margin: 10px 0 6px;
            font-size: 26px;
        }

        .subject-lesson-head h3 {
            margin-bottom: 4px;
            font-size: 20px;
        }

        .subject-lesson-head p,
        .teacher-card p,
        .lesson-card p {
            margin: 0;
            color: #94a3b8;
            line-height: 1.55;
        }

        .lesson-card-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .lesson-card,
        .teacher-card {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            border-radius: 16px;
            background: rgba(30, 41, 59, 0.68);
            border: 1px solid rgba(148, 163, 184, 0.16);
            min-width: 0;
        }

        .lesson-meta,
        .teacher-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: #cbd5e1;
            font-weight: 700;
            font-size: 14px;
        }

        .teacher-card-top .muted {
            flex: 1 1 118px;
            min-width: 0;
            text-align: right;
            line-height: 1.35;
        }

        .group-count-badge {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 30px;
            min-width: 92px;
            padding: 6px 10px;
            border-radius: 999px;
            color: #bae6fd;
            background: rgba(14, 165, 233, 0.13);
            border: 1px solid rgba(56, 189, 248, 0.22);
            white-space: nowrap;
        }

        .group-count-badge strong {
            color: #ffffff;
            font-size: 14px;
            line-height: 1;
        }

        .group-count-badge span {
            font-size: 12px;
            line-height: 1;
        }

        .lesson-card h4,
        .teacher-card h2 {
            margin: 0;
            color: #ffffff;
            font-size: 19px;
            line-height: 1.25;
        }

        .teacher-card p {
            min-height: 48px;
        }

        .lesson-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            margin-top: auto;
        }

        .note-input {
            min-width: 210px;
        }

        .lesson-actions form,
        .actions form,
        .filter-actions form {
            margin: 0;
            display: inline-flex;
        }

        .lesson-actions .btn,
        .lesson-actions button {
            min-height: 38px;
            padding: 9px 13px;
            width: 100%;
        }

        .teacher-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(252px, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .group-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 18px 0;
        }

        .group-chip {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 13px;
            border-radius: 999px;
            color: #dbeafe;
            text-decoration: none;
            background: rgba(30, 41, 59, 0.62);
            border: 1px solid rgba(148, 163, 184, 0.16);
            font-weight: 800;
            transition: 0.2s;
        }

        .group-chip:hover,
        .group-chip.is-active {
            color: #ffffff;
            border-color: rgba(56, 189, 248, 0.42);
            background: rgba(14, 165, 233, 0.18);
            transform: translateY(-1px);
        }

        .group-chip span {
            min-width: 28px;
            min-height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 8px;
            border-radius: 999px;
            color: #bae6fd;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(56, 189, 248, 0.18);
            font-size: 13px;
        }

        .student-group-section {
            display: grid;
            gap: 12px;
            padding: 10px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .student-group-head {
            margin: 0;
            cursor: pointer;
            appearance: none;
            font: inherit;
        }

        .student-group-head[aria-expanded="true"] {
            border-color: rgba(56, 189, 248, 0.5);
        }

        .student-group-head[aria-expanded="true"] .subject-toggle-icon {
            transform: none;
            color: #bbf7d0;
            border-color: rgba(74, 222, 128, 0.28);
        }

        .student-group-icon {
            font-size: 20px;
        }

        .student-group-body[hidden] {
            display: none;
        }

        .student-group-section.is-open {
            border-color: rgba(56, 189, 248, 0.2);
            background: rgba(15, 23, 42, 0.54);
        }

        .student-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .student-card {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            border-radius: 16px;
            background: rgba(30, 41, 59, 0.68);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .student-card h3 {
            margin: 0 0 5px;
            font-size: 19px;
            line-height: 1.25;
        }

        .student-card p,
        .student-card-meta {
            margin: 0;
            color: #94a3b8;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .subject-attendance-trigger {
            cursor: pointer;
            transition: transform 160ms ease, border-color 160ms ease, background-color 160ms ease;
        }

        .subject-attendance-trigger:hover,
        .subject-attendance-trigger:focus-visible {
            transform: translateY(-2px);
            border-color: rgba(56, 189, 248, 0.65);
            background: rgba(30, 50, 74, 0.96);
            outline: none;
        }

        body.dialog-open {
            overflow: hidden;
        }

        .attendance-dialog {
            width: min(780px, calc(100% - 32px));
            max-height: min(84dvh, 820px);
            padding: 0;
            color: var(--text);
            background: #101a2e;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .attendance-dialog::backdrop {
            background: rgba(3, 8, 20, 0.78);
            backdrop-filter: blur(4px);
        }

        .attendance-dialog-shell {
            display: grid;
            grid-template-rows: auto auto minmax(0, 1fr);
            max-height: min(84dvh, 820px);
        }

        .attendance-dialog-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--border);
        }

        .attendance-dialog-head h2 {
            margin: 10px 0 4px;
            font-size: 24px;
        }

        .attendance-dialog-head p {
            margin: 0;
            color: var(--muted);
        }

        .dialog-close {
            display: grid;
            place-items: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            color: var(--text);
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 26px;
            line-height: 1;
        }

        .dialog-close:hover {
            border-color: rgba(56, 189, 248, 0.65);
        }

        .attendance-dialog-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
        }

        .attendance-dialog-summary span {
            padding: 10px 12px;
            color: var(--muted);
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 7px;
        }

        .attendance-dialog-summary strong {
            color: var(--text);
        }

        .attendance-dialog-body {
            min-height: 0;
            padding: 16px 24px 24px;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .student-card-meta {
            display: grid;
            gap: 4px;
            font-size: 14px;
        }

        .student-detail-section {
            display: grid;
            gap: 12px;
            margin-top: 16px;
            padding: 10px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .student-detail-section.is-open {
            border-color: rgba(56, 189, 248, 0.2);
            background: rgba(15, 23, 42, 0.54);
        }

        .student-detail-head {
            margin: 0;
            cursor: pointer;
            appearance: none;
            font: inherit;
        }

        .student-detail-head[aria-expanded="true"] {
            border-color: rgba(56, 189, 248, 0.5);
        }

        .student-detail-head[aria-expanded="true"] .subject-toggle-icon {
            transform: none;
            color: #bbf7d0;
            border-color: rgba(74, 222, 128, 0.28);
        }

        .student-detail-body {
            padding: 0;
        }

        .student-detail-body[hidden] {
            display: none;
        }

        .student-detail-icon {
            font-size: 20px;
        }

        .lesson-mark-tools {
            grid-template-columns: minmax(260px, 1fr) auto;
        }

        .lesson-mark-tools .filter-actions {
            display: grid;
            grid-template-columns: repeat(3, auto);
        }

        .lesson-mark-table {
            margin-top: 0;
        }

        .sticky-actions {
            position: sticky;
            bottom: 0;
            z-index: 5;
            margin-top: 16px;
            padding: 14px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.16);
            backdrop-filter: blur(12px);
        }

        h1, h2, h3 {
            color: white;
            margin-top: 0;
        }

        p {
            color: #cbd5e1;
        }

        a {
            color: #38bdf8;
        }

        a,
        button,
        input,
        select,
        textarea {
            -webkit-tap-highlight-color: transparent;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 3px solid rgba(56, 189, 248, 0.28);
            outline-offset: 2px;
        }

        table {
            width: 100%;
            min-width: 0;
            border-collapse: collapse;
            margin-top: 14px;
            background: rgba(15, 23, 42, 0.55);
            border-radius: 16px;
            overflow: hidden;
            table-layout: auto;
        }

        .table-wrap {
            width: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .table-wrap-fit {
            overflow-x: hidden;
        }

        .table-wrap-fit table {
            min-width: 0;
        }

        .table-wrap-fit th,
        .table-wrap-fit td {
            overflow-wrap: anywhere;
        }

        th, td {
            padding: 13px 15px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
            text-align: left;
            vertical-align: top;
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        th {
            color: #e2e8f0;
            background: rgba(30, 41, 59, 0.88);
            font-weight: 700;
        }

        td {
            color: #cbd5e1;
            overflow-wrap: anywhere;
        }

        input, select, textarea {
            width: 100%;
            min-width: 0;
            min-height: 42px;
            background: rgba(15, 23, 42, 0.9);
            color: white;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 12px;
            padding: 10px 13px;
            outline: none;
        }

        input::placeholder,
        textarea::placeholder {
            color: #64748b;
            opacity: 1;
        }

        input[type="checkbox"] {
            width: 18px;
            min-width: 18px;
            height: 18px;
            min-height: 18px;
            padding: 0;
            accent-color: #38bdf8;
        }

        .checkbox-line {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            min-height: 40px;
            margin: 12px 0 16px;
            padding: 8px 12px;
            color: #dbeafe;
            background: rgba(30, 41, 59, 0.58);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            user-select: none;
            transition: 0.2s;
        }

        .checkbox-line:hover {
            color: #ffffff;
            border-color: rgba(56, 189, 248, 0.32);
            background: rgba(30, 41, 59, 0.78);
        }

        .checkbox-line input[type="checkbox"] {
            appearance: none;
            display: grid;
            place-items: center;
            flex: 0 0 18px;
            border-radius: 6px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.36);
            transition: 0.18s;
        }

        .checkbox-line input[type="checkbox"]::after {
            content: "";
            width: 8px;
            height: 5px;
            margin-top: -2px;
            border-left: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
            transform: rotate(-45deg) scale(0);
            transition: 0.16s;
        }

        .checkbox-line input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            border-color: rgba(125, 211, 252, 0.7);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16);
        }

        .checkbox-line input[type="checkbox"]:checked::after {
            transform: rotate(-45deg) scale(1);
        }

        .checkbox-line span {
            line-height: 1.2;
        }

        textarea {
            resize: vertical;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #38bdf8;
        }

        .btn,
        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            min-width: 0;
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            transition: 0.2s;
            font-family: inherit;
            line-height: 1.2;
            letter-spacing: 0;
            text-transform: none;
            white-space: nowrap;
            box-shadow: 0 12px 28px rgba(2, 6, 23, 0.16);
        }

        .btn:hover,
        button:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
        }

        .btn:disabled,
        button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
            transform: none;
            filter: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: rgba(148, 163, 184, 0.18);
            color: #e5e7eb;
            border: 1px solid rgba(148, 163, 184, 0.14);
            box-shadow: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #06b6d4);
        }

        .btn-success {
            background: linear-gradient(135deg, #16a34a, #22c55e);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #f97316);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #f97316);
        }

        .form-actions-start {
            justify-content: flex-start;
        }

        .subject-card {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 132px;
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 14px;
            text-decoration: none;
            color: #f8fafc;
            background:
                linear-gradient(135deg, rgba(56, 189, 248, 0.16), rgba(34, 197, 94, 0.08)),
                rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(56, 189, 248, 0.28);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.14);
            font-size: 15px;
            font-weight: 750;
            text-align: center;
            transition: 0.2s;
        }

        .subject-card:hover {
            transform: translateY(-2px);
            border-color: rgba(74, 222, 128, 0.55);
            background:
                linear-gradient(135deg, rgba(56, 189, 248, 0.24), rgba(34, 197, 94, 0.16)),
                rgba(30, 41, 59, 0.96);
            box-shadow: 0 14px 30px rgba(8, 145, 178, 0.16);
        }

        .success-message {
            color: #22c55e;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .error-message {
            color: #fb7185;
            margin-bottom: 16px;
            font-weight: 700;
        }

        @media (max-width: 900px) {
            .journal-navbar-inner {
                align-items: center;
                flex-direction: row;
                flex-wrap: wrap;
                padding: 14px 18px;
            }

            .journal-nav {
                width: 100%;
                justify-content: space-between;
                gap: 8px;
            }

            .journal-nav a,
            .journal-logout {
                flex: 1 1 auto;
            }

            .journal-user {
                margin-left: auto;
                max-width: 45vw;
                text-align: right;
            }

            .journal-main {
                padding: 24px 18px;
            }

            .page-header {
                flex-direction: column;
            }

            .page-header .actions,
            .section-actions,
            .actions {
                width: 100%;
            }

            .section-actions {
                justify-content: stretch;
            }

            .section-actions .btn {
                flex: 1;
            }

            .filter-panel,
            .lesson-form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .groups-toolbar {
                grid-template-columns: minmax(0, 1fr) minmax(190px, 0.55fr);
            }

            .filter-panel .field-grow {
                grid-column: 1 / -1;
            }

            .filter-actions {
                grid-column: 1 / -1;
                justify-content: stretch;
                min-width: 0;
            }

            .filter-actions .btn {
                flex: 1;
            }

            .teacher-card p {
                min-height: 0;
            }

            .admin-teacher-grid,
            .admin-current-password {
                grid-template-columns: 1fr;
            }

            .admin-subject-item {
                grid-template-columns: 1fr;
                align-items: start;
            }

            .admin-subject-meta {
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .attendance-dialog {
                width: calc(100% - 20px);
                max-height: 90dvh;
            }

            .attendance-dialog-shell {
                max-height: 90dvh;
            }

            .attendance-dialog-head {
                gap: 12px;
                padding: 18px;
            }

            .attendance-dialog-head h2 {
                margin-top: 8px;
                font-size: 21px;
            }

            .attendance-dialog-summary {
                grid-template-columns: 1fr;
                padding: 12px 18px;
            }

            .attendance-dialog-body {
                padding: 12px 18px 18px;
            }

            .journal-navbar {
                position: sticky;
            }

            .journal-navbar-inner {
                padding: 14px 16px 12px;
                gap: 10px;
            }

            .journal-logo {
                font-size: 26px;
            }

            .app-menu-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                margin-left: auto;
                padding: 10px 14px;
                color: #ffffff;
                font-weight: 800;
                border-radius: 13px;
                border: 1px solid rgba(125, 211, 252, 0.25);
                background: rgba(30, 41, 59, 0.82);
                cursor: pointer;
            }

            .journal-user {
                order: 2;
                flex: 1 1 100%;
                max-width: none;
                margin-left: 0;
                text-align: left;
                font-size: 14px;
            }

            .journal-nav {
                position: fixed;
                left: 12px;
                right: 12px;
                top: 72px;
                z-index: 60;
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
                align-items: stretch;
                justify-content: flex-start;
                width: auto;
                max-height: min(70dvh, 520px);
                padding: 18px;
                gap: 10px;
                background:
                    linear-gradient(180deg, rgba(30, 41, 59, 0.98), rgba(15, 23, 42, 0.98)),
                    #0f172a;
                border: 1px solid rgba(125, 211, 252, 0.22);
                border-radius: 24px;
                box-shadow: 0 24px 90px rgba(0, 0, 0, 0.48);
                transform: translateY(-16px) scale(0.98);
                transition: transform 0.24s ease, opacity 0.24s ease;
                overflow-y: auto;
                opacity: 0;
                pointer-events: none;
            }

            .journal-nav::before {
                content: '';
                width: 46px;
                height: 5px;
                margin: 0 auto 4px;
                border-radius: 999px;
                background: rgba(148, 163, 184, 0.35);
            }

            .journal-nav form {
                min-width: 0;
            }

            .journal-nav a,
            .journal-logout {
                width: 100%;
                min-height: 54px;
                padding: 14px 16px;
                font-size: 16px;
                white-space: nowrap;
                text-align: left;
                justify-content: flex-start;
                border-radius: 17px;
                background: rgba(15, 23, 42, 0.46);
                border-color: rgba(148, 163, 184, 0.14);
            }

            .app-menu-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 6px;
                color: #ffffff;
                font-size: 24px;
                font-weight: 800;
            }

            .app-menu-close {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 46px;
                color: #e5e7eb;
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: rgba(15, 23, 42, 0.72);
                cursor: pointer;
                font-size: 26px;
                line-height: 1;
            }

            .app-menu-overlay {
                position: fixed;
                inset: 0;
                z-index: 55;
                background: rgba(2, 6, 23, 0.86);
                touch-action: none;
                display: none;
            }

            .app-menu-toggle:checked ~ .app-menu-overlay {
                display: block;
            }

            .app-menu-toggle:checked ~ .journal-nav {
                transform: translateY(0);
                opacity: 1;
                pointer-events: auto;
            }

            .journal-main {
                padding: 18px 12px;
            }

            .container {
                border-radius: 16px;
                padding: 18px 16px;
            }

            .panel {
                padding: 16px;
            }

            .page-title {
                font-size: 28px;
            }

            .page-subtitle {
                font-size: 14px;
            }

            .stat-grid,
            .dashboard-grid,
            .form-grid,
            .admin-two-column,
            .group-builder,
            .filter-panel,
            .lesson-form-grid,
            .lesson-card-list,
            .teacher-card-grid,
            .student-card-grid {
                grid-template-columns: 1fr;
            }

            .group-chip-list {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .group-chip {
                justify-content: space-between;
            }

            .group-builder-row {
                grid-template-columns: 1fr;
            }

            .group-code-card {
                min-height: 150px;
            }

            .group-code-value {
                font-size: 34px;
            }

            .lesson-mark-tools {
                grid-template-columns: 1fr;
            }

            .lesson-mark-tools .filter-actions {
                grid-template-columns: 1fr;
            }

            .filter-panel {
                padding: 14px;
            }

            .admin-lesson-item {
                grid-template-columns: 1fr;
            }

            .admin-list-panel,
            .form-card {
                padding: 16px;
            }

            .filter-actions,
            .lesson-actions,
            .subject-lesson-head,
            .workspace-head {
                align-items: stretch;
            }

            .subject-toggle {
                align-items: stretch;
                flex-direction: column;
                gap: 12px;
            }

            .subject-toggle-meta {
                justify-content: space-between;
            }

            .selected-subject-head {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .lesson-actions {
                grid-template-columns: 1fr;
            }

            .note-input {
                min-width: 0;
            }

            .filter-actions .btn,
            .lesson-actions .btn,
            .lesson-actions button,
            .lesson-actions form {
                width: 100%;
            }

            .stat-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
            }

            .stat-card {
                padding: 14px 12px;
                border-radius: 14px;
            }

            .stat-value {
                font-size: 28px;
                margin-bottom: 6px;
            }

            .stat-label {
                font-size: 12px;
                line-height: 1.3;
            }

            .dashboard-card {
                padding: 18px;
                border-radius: 16px;
            }

            .dashboard-card h3 {
                font-size: 22px;
            }

            .actions .btn,
            .actions button,
            .btn {
                width: 100%;
            }

            .subject-card {
                width: 100%;
            }

            table {
                min-width: 0;
            }

            table.responsive-table,
            table.responsive-table thead,
            table.responsive-table tbody,
            table.responsive-table tr,
            table.responsive-table th,
            table.responsive-table td {
                display: block;
                width: 100%;
            }

            table.responsive-table {
                background: transparent;
                border-radius: 0;
            }

            table.responsive-table thead {
                display: none;
            }

            table.responsive-table tr {
                margin-top: 12px;
                padding: 14px;
                border-radius: 16px;
                background: rgba(30, 41, 59, 0.72);
                border: 1px solid rgba(148, 163, 184, 0.16);
            }

            table.responsive-table td {
                display: grid;
                grid-template-columns: 116px 1fr;
                gap: 12px;
                padding: 9px 0;
                border: none;
                font-size: 14px;
                align-items: center;
            }

            table.responsive-table td .actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 8px;
            }

            table.responsive-table td .actions .btn,
            table.responsive-table td .actions button,
            table.responsive-table td .actions form {
                width: 100%;
            }

            table.responsive-table td::before {
                content: attr(data-label);
                color: #94a3b8;
                font-weight: 700;
            }

            th, td {
                padding: 12px;
                font-size: 14px;
            }
        }

        @media (max-width: 420px) {
            .container {
                padding: 16px 14px;
            }

            .stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            table.responsive-table td {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .password-line,
            .credential-row {
                grid-template-columns: 1fr;
            }

            .btn-compact {
                width: 100%;
            }
        }

        .app-refresh-button {
            flex: 0 0 auto;
            display: none;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(91, 213, 255, 0.38);
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.28);
            font: inherit;
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
        }

        .journal-native-app .app-refresh-button {
            display: inline-flex;
        }

        .app-refresh-button:active {
            transform: translateY(1px);
        }

        @media (max-width: 640px) {
            .app-refresh-button {
                width: 40px;
                height: 40px;
                font-size: 19px;
            }
        }
    </style>
</head>

<body>
    <div class="journal-layout">
        <header class="journal-navbar">
            <div class="journal-navbar-inner">
                <div class="journal-brand-row">
                    <a href="{{ route('welcome') }}" class="journal-logo">
                        Journal<span>.</span>
                    </a>

                    <button type="button" class="app-refresh-button" id="appRefreshButton" aria-label="Обновить страницу" title="Обновить страницу">
                        ↻
                    </button>
                </div>

                @auth
                    <input type="checkbox" id="app-menu-toggle" class="app-menu-toggle" aria-hidden="true">
                    <label for="app-menu-toggle" class="app-menu-button">Меню</label>
                    <label for="app-menu-toggle" class="app-menu-overlay" aria-hidden="true"></label>

                    <nav class="journal-nav" aria-label="Основное меню">
                        <div class="app-menu-head">
                            <span>Меню</span>
                            <label for="app-menu-toggle" class="app-menu-close" aria-label="Закрыть меню">×</label>
                        </div>

                        <a href="{{ route('dashboard') }}">Главная</a>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.teachers.index') }}">Учителя</a>
                            <a href="{{ route('students.index') }}">Студенты</a>
                        @elseif(auth()->user()->role === 'teacher')
                            <a href="{{ route('groups.index') }}">Группы</a>
                            <a href="{{ route('students.index') }}">Студенты</a>
                            <a href="{{ route('subjects.index') }}">Предметы</a>
                        @elseif(auth()->user()->role === 'student')
                            <a href="{{ route('student.attendance.history') }}">Посещаемость</a>
                        @endif

                        <span class="journal-user">
                            {{ auth()->user()->name }}
                        </span>

                        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Вы действительно хотите выйти из аккаунта?')">
                            @csrf
                            <button type="submit" class="journal-logout">
                                Выйти
                            </button>
                        </form>
                    </nav>
                @endauth
            </div>
        </header>

        <main class="journal-main">
            @yield('content')
        </main>
    </div>

    <script>
        (() => {
            const markerKey = 'journal_native_app';
            const params = new URLSearchParams(window.location.search);
            const appMarker = params.get('journal_app');

            if (appMarker === 'windows' || appMarker === 'android') {
                localStorage.setItem(markerKey, appMarker);
            }

            if (!localStorage.getItem(markerKey)) {
                return;
            }

            document.documentElement.classList.add('journal-native-app');

            const refreshButton = document.getElementById('appRefreshButton');
            refreshButton?.addEventListener('click', () => {
                window.location.reload();
            });
        })();

        (() => {
            const toggle = document.getElementById('app-menu-toggle');
            let lockedScrollY = 0;

            if (!toggle) {
                return;
            }

            const closeMenu = () => {
                const wasOpen = document.body.classList.contains('app-menu-open');
                toggle.checked = false;
                document.documentElement.classList.remove('app-menu-open');
                document.body.classList.remove('app-menu-open');
                if (wasOpen) {
                    window.scrollTo(0, lockedScrollY);
                }
            };

            toggle.addEventListener('change', () => {
                if (toggle.checked) {
                    lockedScrollY = window.scrollY;
                    document.documentElement.classList.add('app-menu-open');
                    document.body.classList.add('app-menu-open');

                    if (history.state?.appMenu !== true) {
                        history.pushState({ appMenu: true }, '', window.location.href);
                    }
                } else {
                    const wasOpen = document.body.classList.contains('app-menu-open');
                    document.documentElement.classList.remove('app-menu-open');
                    document.body.classList.remove('app-menu-open');
                    if (wasOpen) {
                        window.scrollTo(0, lockedScrollY);
                    }
                }
            });

            document.querySelectorAll('.journal-nav a, .journal-logout').forEach((item) => {
                item.addEventListener('click', closeMenu);
            });

            window.addEventListener('popstate', () => {
                if (toggle.checked) {
                    closeMenu();
                }
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && toggle.checked) {
                    closeMenu();
                }
            });
        })();
    </script>
</body>
</html>
