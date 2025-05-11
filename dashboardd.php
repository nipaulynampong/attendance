<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Monitoring System</title>
    <link rel="icon" href="1.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="iframe-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      /* Base styles */
      :root {
        --primary: #4f6f52;
        --primary-dark: #3e5840;
        --primary-light: #607d63;
        --primary-lighter: rgba(79, 111, 82, 0.15);
        --secondary: #9cafaa;
        --secondary-light: #bdd0ca;
        --background: #F5F5F0;
        --background-alt: #ECECEA;
        --text-dark: #3e3f5b;
        --text-light: #f5efe6;
        --transition-slow: 0.4s;
        --transition-medium: 0.3s;
        --transition-fast: 0.2s;
        --shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
      }
      
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
      }
      
      @keyframes slideInRight {
        from { transform: translateX(20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      
      @keyframes slideInLeft {
        from { transform: translateX(-20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      
      @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
      }
      
      @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
        100% { transform: translateY(0px); }
      }
      
      @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
      
      @keyframes breathe {
        0% { box-shadow: 0 0 0 0 rgba(79, 111, 82, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(79, 111, 82, 0); }
        100% { box-shadow: 0 0 0 0 rgba(79, 111, 82, 0); }
      }
      
      body {
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
        margin: 0;
        padding: 16px;
        background-color: #f5efe6 !important;
        background-image: none !important; /* Remove gradient overlays */
        background-attachment: fixed;
        overflow-x: hidden;
        min-height: 100%;
        transition: background-color var(--transition-slow);
      }
      
      /* Style for quick links container */
      .quick-links {
        background-color: #f5efe6 !important;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
      }
      
      .quick-links-container {
        background-color: #f5efe6 !important;
      }

      .separator {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto 12px auto;
        border-bottom: 1px solid var(--secondary);
        position: relative;
      }
      
      .separator::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), transparent);
      }

      .dashboard {
        font-size: 24px;
        color: var(--text-dark);
        margin: 5px 0;
        font-weight: 700;
        animation: fadeIn 0.8s ease-out;
        position: relative;
        display: flex;
        align-items: center;
        letter-spacing: 0.5px;
      }
      
      .dashboard::before {
        content: '📊';
        margin-right: 10px;
        animation: pulse 2s infinite;
      }
      
      .dashboard::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), transparent);
        border-radius: 10px;
      }

      .big-box {
        background: var(--primary);
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        width: 100%;
        max-width: 1200px;
        padding: 25px;
        margin: 20px auto;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--text-light);
        box-shadow: var(--shadow-md);
        transition: all var(--transition-medium);
        animation: fadeIn 0.6s ease-out;
        position: relative;
        overflow: hidden;
        z-index: 1;
      }
      
      .big-box::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
        pointer-events: none;
        z-index: -1;
      }
      
      .big-box::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        z-index: 2;
      }
      
      .big-box:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
      }
      
      .big-box .pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='.http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.1;
        z-index: -1;
      }

      .welcome-text {
        font-size: 1.3em;
        max-width: 60%;
        animation: slideInLeft 0.7s ease-out;
        position: relative;
        z-index: 1;
        font-weight: 500;
        display: flex;
        align-items: center;
      }
      
      .welcome-text img {
        animation: pulse 3s infinite;
        filter: drop-shadow(0 0 8px rgba(255,255,255,0.3));
        margin-right: 15px !important;
      }

      .manual-attendance-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: var(--primary);
        color: var(--text-light);
        padding: 12px 24px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 1em;
        margin: 25px auto;
        transition: all var(--transition-medium);
        max-width: 1200px;
        width: fit-content;
        border: none;
        box-shadow: var(--shadow-md), 0 0 0 0 rgba(79, 111, 82, 0);
        position: relative;
        overflow: hidden;
        z-index: 1;
        font-weight: 500;
        letter-spacing: 0.5px;
        animation: fadeIn 0.8s ease-out, breathe 2s infinite;
      }
      
      .manual-attendance-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        z-index: -1;
        transition: transform 0.8s;
      }

      .manual-attendance-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-3px) scale(1.03);
        box-shadow: var(--shadow-lg), 0 0 0 0 rgba(79, 111, 82, 0);
        letter-spacing: 1px;
      }
      
      .manual-attendance-btn:hover::before {
        transform: translateX(100%);
      }
      
      .manual-attendance-btn i {
        transition: all var(--transition-medium);
        font-size: 1.1em;
      }
      
      .manual-attendance-btn:hover i {
        transform: rotate(-10deg) translateX(-3px);
      }

      .boxes {
        display: flex;
        justify-content: space-between;
        max-width: 1200px;
        margin: 25px auto;
        flex-wrap: wrap;
        gap: 20px;
      }

      .box {
        flex: 1;
        min-width: 160px;
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        padding: 20px;
        margin: 0;
        transition: all var(--transition-medium);
        position: relative;
        overflow: hidden;
        animation: fadeIn 0.8s ease-out;
        animation-fill-mode: both;
        z-index: 1;
        border: 1px solid rgba(156, 175, 170, 0.1);
      }
      
      .box:nth-child(1) { animation-delay: 0.1s; }
      .box:nth-child(2) { animation-delay: 0.2s; }
      .box:nth-child(3) { animation-delay: 0.3s; }
      .box:nth-child(4) { animation-delay: 0.4s; }
      
      .box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        z-index: 1;
      }
      
      .box::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 30px 30px 0;
        border-color: transparent var(--primary-lighter) transparent transparent;
        transition: all var(--transition-medium);
      }
      
      .box .box-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234f6f52' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: -1;
        opacity: 0;
        transition: opacity var(--transition-medium);
      }

      .box:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: var(--shadow-lg);
      }
      
      .box:hover::after {
        border-width: 0 40px 40px 0;
      }
      
      .box:hover .box-bg {
        opacity: 1;
      }

      .box h2 {
        font-size: 0.9em;
        margin-bottom: 12px;
        color: var(--secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      
      .box h2 i {
        font-size: 1.1em;
        background: var(--background);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        transition: all var(--transition-medium);
        box-shadow: inset 0 0 0 1px rgba(79, 111, 82, 0.1);
      }
      
      .box:hover h2 i {
        background: var(--primary);
        color: var(--text-light);
        transform: rotate(360deg);
      }

      .box p {
        font-size: 2em;
        font-weight: 700;
        margin: 5px 0 0 0;
        color: var(--text-dark);
        transition: all var(--transition-medium);
        position: relative;
        display: inline-block;
      }
      
      .box p::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-lighter), transparent);
        opacity: 0;
        transition: all var(--transition-medium);
      }
      
      .box:hover p {
        transform: scale(1.1);
        color: var(--primary);
      }
      
      .box:hover p::after {
        opacity: 1;
        width: 100%;
      }
      
      .time {
        font-size: 1.4em;
        color: var(--text-light);
        font-weight: 600;
        animation: fadeIn 1s ease-out;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
      }
      
      .time::before {
        content: '\f017';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        margin-right: 10px;
        animation: pulse 2s infinite;
      }

      .charts-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        max-width: 1200px;
        margin: 30px auto;
        animation: fadeIn 1s ease-out;
      }

      .chart-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        padding: 25px;
        height: 300px;
        transition: all var(--transition-medium);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(156, 175, 170, 0.1);
      }
      
      .chart-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        opacity: 0;
        transition: all var(--transition-medium);
      }
      
      .chart-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 30px 30px 0;
        border-color: transparent var(--primary-lighter) transparent transparent;
        transition: all var(--transition-medium);
      }
      
      .chart-wrapper .chart-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234f6f52' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: -1;
        opacity: 0;
        transition: opacity var(--transition-medium);
      }
      
      .chart-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
      }
      
      .chart-wrapper:hover::before {
        opacity: 1;
      }
      
      .chart-wrapper:hover::after {
        border-width: 0 40px 40px 0;
      }
      
      .chart-wrapper:hover .chart-bg {
        opacity: 1;
      }

      .chart-wrapper.full-width {
        grid-column: 1 / -1;
      }

      @media (max-width: 1200px) {
        .charts-container {
          grid-template-columns: 1fr;
        }
        
        .chart-wrapper.full-width {
          grid-column: auto;
        }
      }

      .top-employees {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        padding: 30px;
        margin: 30px auto 0 auto;
        max-width: 1200px;
        transition: all var(--transition-medium);
        animation: fadeIn 1s ease-out;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(156, 175, 170, 0.1);
      }
      
      .top-employees::before {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: var(--primary-lighter);
        border-radius: 50%;
        transform: translate(50%, 50%);
        z-index: 0;
        opacity: 0.5;
      }
      
      .top-employees:hover {
        box-shadow: var(--shadow-md);
      }

      .top-employees h2 {
        color: var(--text-dark);
        font-size: 22px;
        margin-bottom: 25px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--primary);
        position: relative;
        font-weight: 600;
        z-index: 1;
      }

      .top-employees h2:after {
        content: '🏆';
        position: absolute;
        right: 0;
        top: 0;
        animation: pulse 2s infinite;
      }

      .employee-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 20px;
        position: relative;
        z-index: 1;
      }

      .employee-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all var(--transition-medium);
        border: 1px solid var(--background-alt);
        position: relative;
        overflow: hidden;
        animation: fadeIn 0.8s ease-out;
        animation-fill-mode: both;
        z-index: 1;
        box-shadow: var(--shadow-sm);
      }
      
      .employee-card:nth-child(1) { animation-delay: 0.1s; }
      .employee-card:nth-child(2) { animation-delay: 0.2s; }
      .employee-card:nth-child(3) { animation-delay: 0.3s; }
      .employee-card:nth-child(4) { animation-delay: 0.4s; }
      .employee-card:nth-child(5) { animation-delay: 0.5s; }
      
      .employee-card .card-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234f6f52' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: -1;
        opacity: 0;
        transition: opacity var(--transition-medium);
      }
      
      .employee-card:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--primary);
        transition: all var(--transition-medium);
      }

      .employee-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-lg);
      }
      
      .employee-card:hover:before {
        width: 8px;
      }
      
      .employee-card:hover .card-bg {
        opacity: 1;
      }

      .employee-card img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--background);
        transition: all var(--transition-medium);
        box-shadow: var(--shadow-sm);
      }
      
      .employee-card:hover img {
        transform: scale(1.1) rotate(5deg);
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px rgba(79, 111, 82, 0.2);
      }

      .employee-info {
        flex: 1;
      }

      .employee-info h3 {
        margin: 0;
        font-size: 17px;
        color: var(--text-dark);
        font-weight: 600;
        margin-bottom: 6px;
        transition: all var(--transition-medium);
      }
      
      .employee-card:hover .employee-info h3 {
        color: var(--primary);
        transform: translateX(3px);
      }

      .employee-info .department {
        color: var(--secondary);
        font-size: 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all var(--transition-medium);
      }
      
      .employee-card:hover .department {
        transform: translateX(3px);
      }

      .employee-info .attendance {
        background: var(--background);
        color: var(--primary);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all var(--transition-medium);
        font-weight: 500;
        box-shadow: inset 0 0 0 1px rgba(79, 111, 82, 0.1);
      }
      
      .employee-card:hover .attendance {
        background: var(--primary);
        color: var(--text-light);
        transform: translateX(3px);
        box-shadow: 0 3px 10px rgba(79, 111, 82, 0.2);
      }

      .quick-links {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        padding: 30px;
        margin: 30px auto;
        max-width: 1200px;
        transition: all var(--transition-medium);
        animation: fadeIn 0.8s ease-out;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(156, 175, 170, 0.1);
      }
      
      .quick-links::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 200px;
        height: 200px;
        background: var(--primary-lighter);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        z-index: 0;
        opacity: 0.5;
      }
      
      .quick-links:hover {
        box-shadow: var(--shadow-md);
      }

      .quick-links h2 {
        color: var(--text-dark);
        font-size: 22px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--primary);
        position: relative;
        font-weight: 600;
        z-index: 1;
      }
      
      .quick-links h2:after {
        content: '🔗';
        position: absolute;
        right: 0;
        top: 0;
        animation: pulse 2s infinite;
      }

      .quick-links-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        position: relative;
        z-index: 1;
      }

      .quick-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        background: white;
        padding: 20px 10px;
        border-radius: 16px;
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 500;
        transition: all var(--transition-medium);
        border: 1px solid var(--background-alt);
        position: relative;
        overflow: hidden;
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
        box-shadow: var(--shadow-sm);
      }
      
      .quick-link:nth-child(1) { animation-delay: 0.1s; }
      .quick-link:nth-child(2) { animation-delay: 0.2s; }
      .quick-link:nth-child(3) { animation-delay: 0.3s; }
      .quick-link:nth-child(4) { animation-delay: 0.4s; }
      .quick-link:nth-child(5) { animation-delay: 0.5s; }
      .quick-link:nth-child(6) { animation-delay: 0.6s; }
      .quick-link:nth-child(7) { animation-delay: 0.7s; }
      
      .quick-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        opacity: 0;
        transition: all var(--transition-medium);
        z-index: 0;
      }
      
      .quick-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), transparent);
        opacity: 0;
        transition: all var(--transition-medium);
      }

      .quick-link:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        color: var(--text-light);
        border-color: transparent;
      }
      
      .quick-link:hover::before {
        opacity: 1;
      }
      
      .quick-link:hover::after {
        opacity: 1;
      }

      .quick-link i {
        font-size: 22px;
        margin-bottom: 12px;
        background: var(--background);
        width: 50px;
        height: 50px;
        line-height: 50px;
        border-radius: 50%;
        color: var(--primary);
        transition: all var(--transition-medium);
        position: relative;
        z-index: 1;
        box-shadow: inset 0 0 0 1px rgba(79, 111, 82, 0.1);
      }

      .quick-link:hover i {
        background: var(--text-light);
        color: var(--primary);
        transform: scale(1.1) rotate(10deg);
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
      }
      
      .quick-link span {
        position: relative;
        z-index: 1;
        font-weight: 500;
        transition: all var(--transition-medium);
      }
      
      .quick-link:hover span {
        transform: scale(1.05);
        letter-spacing: 0.5px;
      }

      table {
        width: 100%;
        max-width: 1200px;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-medium);
        position: relative;
        border: 1px solid rgba(156, 175, 170, 0.1);
      }
      
      table:hover {
        box-shadow: var(--shadow-md);
      }

      th {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: var(--text-light);
        font-weight: 600;
        text-align: left;
        padding: 18px 20px;
        text-transform: uppercase;
        font-size: 0.8em;
        letter-spacing: 0.8px;
      }

      td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--background-alt);
        color: var(--text-dark);
        transition: all var(--transition-fast);
      }

      tr:last-child td {
        border-bottom: none;
      }

      tr:hover td {
        background-color: rgba(79, 111, 82, 0.05);
      }

      .attendance-heading {
        font-size: 22px;
        color: var(--text-dark);
        margin: 25px 0 15px 0;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        font-weight: 600;
        position: relative;
        display: inline-block;
      }
      
      .attendance-heading::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), transparent);
        border-radius: 10px;
      }

      .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: var(--text-light);
        border: none;
        padding: 12px 24px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 500;
        transition: all var(--transition-medium);
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        letter-spacing: 0.5px;
      }
      
      .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transform: translateX(-100%);
        z-index: -1;
        transition: transform 0.8s;
      }

      .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
        letter-spacing: 1px;
      }
      
      .btn-primary:hover::before {
        transform: translateX(100%);
      }
      
      .btn-primary i {
        transition: all var(--transition-medium);
        font-size: 1.1em;
      }
      
      .btn-primary:hover i {
        transform: translateX(3px);
      }
      
      /* Loading shimmer effect */
      .shimmer {
        background: linear-gradient(90deg, var(--background) 0px, rgba(255, 255, 255, 0.8) 50%, var(--background) 100%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite linear;
      }
      
      /* Tooltip styling */
      [data-tooltip] {
        position: relative;
        cursor: pointer;
      }
      
      [data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-5px);
        background: var(--text-dark);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        transition: all var(--transition-medium);
        z-index: 10;
      }
      
      [data-tooltip]:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-10px);
      }
    </style>
</head>
<body>

<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Get today's date
$currentDate = date('Y-m-d');

// Query to get attendance count for today
$sqlAttendanceToday = "SELECT COUNT(*) as presentToday FROM attendance WHERE LOGDATE = '$currentDate'";
$resultAttendanceToday = $conn->query($sqlAttendanceToday);
$presentToday = 0;
if ($resultAttendanceToday && $resultAttendanceToday->num_rows > 0) {
    $row = $resultAttendanceToday->fetch_assoc();
    $presentToday = $row['presentToday'];
}

// Query to get department data
$sqlDepartmentData = "SELECT * FROM department LIMIT 10";
$resultDepartmentData = $conn->query($sqlDepartmentData);

// Query to get employee data
$sqlEmployeeData = "SELECT * FROM employee LIMIT 10";
$resultEmployeeData = $conn->query($sqlEmployeeData);

// Query to get department data with employee count
$sqlDepartmentCount = "SELECT department.Department, COUNT(employee.EmployeeID) as employee_count 
                       FROM department 
                       LEFT JOIN employee ON department.Department = employee.Department 
                       GROUP BY department.Department";
$resultDepartmentCount = $conn->query($sqlDepartmentCount);

// Query to get attendance data for the past 7 days
$sqlWeeklyAttendance = "SELECT DATE(LOGDATE) as date, COUNT(*) as attendance_count 
                        FROM attendance 
                        WHERE LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
                        GROUP BY DATE(LOGDATE)
                        ORDER BY DATE(LOGDATE)";
$resultWeeklyAttendance = $conn->query($sqlWeeklyAttendance);

// Prepare data for charts
$departmentLabels = [];
$employeeCounts = [];
while($row = $resultDepartmentCount->fetch_assoc()) {
    $departmentLabels[] = $row['Department'];
    $employeeCounts[] = $row['employee_count'];
}

$dateLabels = [];
$attendanceCounts = [];
while($row = $resultWeeklyAttendance->fetch_assoc()) {
    $dateLabels[] = date('M d', strtotime($row['date']));
    $attendanceCounts[] = $row['attendance_count'];
}

// Query for monthly attendance statistics
$sqlMonthlyStats = "SELECT 
    YEAR(LOGDATE) as year,
    MONTH(LOGDATE) as month,
    COUNT(*) as attendance_count
FROM attendance 
WHERE LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
GROUP BY YEAR(LOGDATE), MONTH(LOGDATE)
ORDER BY year ASC, month ASC";
$resultMonthlyStats = $conn->query($sqlMonthlyStats);

// Query for top 5 employees with best attendance
$sqlTopEmployees = "SELECT 
    e.`Last Name` as LastName,
    e.`First Name` as FirstName,
    e.Department,
    e.EmployeeID,
    e.Image,
    COUNT(a.LOGDATE) as attendance_count 
FROM employee e 
LEFT JOIN attendance a ON e.EmployeeID = a.EMPLOYEEID 
WHERE a.LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
GROUP BY e.EmployeeID 
ORDER BY attendance_count DESC 
LIMIT 5";

$resultTopEmployees = $conn->query($sqlTopEmployees);

// Query for early arrivals
$sqlEarlyArrivals = "SELECT 
    COUNT(*) as early_count 
FROM attendance 
WHERE TIME(TIMEIN) <= '08:00:00' 
AND LOGDATE = CURRENT_DATE";
$resultEarlyArrivals = $conn->query($sqlEarlyArrivals);
$earlyArrivals = $resultEarlyArrivals->fetch_assoc()['early_count'];

// Query for absences today
$sqlAbsences = "SELECT 
    (SELECT COUNT(*) FROM employee) - 
    (SELECT COUNT(DISTINCT EmployeeID) FROM attendance WHERE LOGDATE = CURRENT_DATE) 
    as absent_count";
$resultAbsences = $conn->query($sqlAbsences);
$absentToday = $resultAbsences->fetch_assoc()['absent_count'];

// Prepare monthly data
$monthLabels = [];
$monthlyAttendance = [];
while($row = $resultMonthlyStats->fetch_assoc()) {
    $monthName = date("M Y", mktime(0, 0, 0, $row['month'], 1, $row['year']));
    $monthLabels[] = $monthName;
    $monthlyAttendance[] = $row['attendance_count'];
}

// Query for monthly comparison of present, late, and absent
$sqlAttendanceComparison = "SELECT 
    YEAR(a.LOGDATE) as year,
    MONTH(a.LOGDATE) as month,
    SUM(CASE WHEN a.TIMEIN IS NOT NULL THEN 1 ELSE 0 END) as present_count,
    SUM(CASE WHEN TIME(a.TIMEIN) > '08:00:00' THEN 1 ELSE 0 END) as late_count,
    (SELECT COUNT(*) FROM employee) - COUNT(DISTINCT a.EMPLOYEEID) as absent_count
FROM 
    (SELECT DISTINCT LOGDATE FROM attendance WHERE LOGDATE >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)) as dates
CROSS JOIN 
    employee e
LEFT JOIN 
    attendance a ON e.EmployeeID = a.EMPLOYEEID AND dates.LOGDATE = a.LOGDATE
GROUP BY 
    YEAR(dates.LOGDATE), MONTH(dates.LOGDATE)
ORDER BY 
    year ASC, month ASC";

$resultAttendanceComparison = $conn->query($sqlAttendanceComparison);

// Prepare comparison data
$comparisonMonths = [];
$presentData = [];
$lateData = [];
$absentData = [];

if ($resultAttendanceComparison) {
    while($row = $resultAttendanceComparison->fetch_assoc()) {
        $monthName = date("M Y", mktime(0, 0, 0, $row['month'], 1, $row['year']));
        $comparisonMonths[] = $monthName;
        $presentData[] = $row['present_count'];
        $lateData[] = $row['late_count'];
        $absentData[] = $row['absent_count'];
    }
}
?>

    <div class="separator">
        <h2 class="dashboard">Analytics Dashboard</h2>
    </div>
    
    <div class="big-box">
        <div class="pattern"></div>
        <div class="welcome-text">
            <i class="fas fa-chart-line" style="font-size: 2.2rem; color: var(--text-light); margin-right: 15px; text-shadow: 0 0 8px rgba(255,255,255,0.3);"></i>
            Welcome to Attendance Management System
        </div>
        <span class="time" id="clock"></span>
    </div>

    <a href="manualattendance.php" class="manual-attendance-btn" data-tooltip="Enter attendance manually">
        <i class="fas fa-edit"></i> Manual Attendance Entry
    </a>

<div class="quick-links">
    <h2>Quick Links</h2>
    <div class="quick-links-container">
        <a href="addemployees.php" class="quick-link" target="mainFrame" data-tooltip="Add new employee records">
            <i class="fas fa-user-plus"></i> <span>Add Employee</span>
        </a>
        <a href="employee.php" class="quick-link" target="mainFrame" data-tooltip="View all employees">
            <i class="fas fa-users"></i> <span>View Employees</span>
        </a>
        <a href="department.php" class="quick-link" target="mainFrame" data-tooltip="Manage departments">
            <i class="fas fa-building"></i> <span>Department</span>
        </a>
        <a href="holidays.php" class="quick-link" target="mainFrame" data-tooltip="View & manage holidays">
            <i class="fas fa-calendar-alt"></i> <span>Holidays</span>
        </a>
        <a href="events.php" class="quick-link" target="mainFrame" data-tooltip="View & manage events">
            <i class="fas fa-calendar-check"></i> <span>Events</span>
        </a>
        <a href="attendancereport.php" class="quick-link" target="mainFrame" data-tooltip="View individual reports">
            <i class="fas fa-file-alt"></i> <span>Individual Report</span>
        </a>
        <a href="attendance_view.php" class="quick-link" target="mainFrame" data-tooltip="View attendance records">
            <i class="fas fa-clipboard-check"></i> <span>Attendance Record</span>
        </a>
    </div>
</div>

    <div class="boxes">
        <div class="box">
            <div class="box-bg"></div>
            <h2><i class="fas fa-users"></i> Present Today</h2>
            <p><?php echo $presentToday; ?></p>
        </div>
        <div class="box">
            <div class="box-bg"></div>
            <h2><i class="fas fa-clock"></i> Early Arrivals</h2>
            <p><?php echo $earlyArrivals; ?></p>
        </div>
        <div class="box">
            <div class="box-bg"></div>
            <h2><i class="fas fa-user-times"></i> Absent Today</h2>
            <p><?php echo $absentToday; ?></p>
        </div>
        <div class="box">
            <div class="box-bg"></div>
            <h2><i class="fas fa-id-card"></i> Total Employees</h2>
            <p><?php echo $resultEmployeeData->num_rows; ?></p>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart-wrapper">
            <div class="chart-bg"></div>
            <canvas id="departmentChart"></canvas>
        </div>
        <div class="chart-wrapper">
            <div class="chart-bg"></div>
            <canvas id="weeklyAttendanceChart"></canvas>
        </div>
        <div class="chart-wrapper full-width">
            <div class="chart-bg"></div>
            <canvas id="monthlyAttendanceChart"></canvas>
        </div>
        <div class="chart-wrapper full-width">
            <div class="chart-bg"></div>
            <canvas id="attendanceComparisonChart"></canvas>
        </div>
    </div>

<script>
// Department Distribution Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(departmentCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($departmentLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($employeeCounts); ?>,
            backgroundColor: [
                '#4f6f52',
                '#9cafaa',
                '#3e3f5b',
                '#607d63',
                '#8a9a97',
                '#54556e'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Employee Distribution by Department',
                font: {
                    size: 14,
                    weight: 'bold'
                },
                color: '#3e3f5b'
            },
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 10,
                    color: '#3e3f5b'
                }
            }
        }
    }
});

// Weekly Attendance Chart
const weeklyCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dateLabels); ?>,
        datasets: [{
            label: 'Daily Attendance',
            data: <?php echo json_encode($attendanceCounts); ?>,
            fill: true,
            backgroundColor: 'rgba(79, 111, 82, 0.1)',
            borderColor: '#4f6f52',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Weekly Attendance Overview',
                font: {
                    size: 14,
                    weight: 'bold'
                },
                color: '#3e3f5b'
            },
            legend: {
                labels: {
                    color: '#3e3f5b'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    color: '#3e3f5b'
                },
                grid: {
                    color: 'rgba(156, 175, 170, 0.2)'
                }
            },
            x: {
                ticks: {
                    color: '#3e3f5b'
                },
                grid: {
                    color: 'rgba(156, 175, 170, 0.2)'
                }
            }
        }
    }
});

// Monthly Attendance Chart
const monthlyCtx = document.getElementById('monthlyAttendanceChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($monthLabels); ?>,
        datasets: [{
            label: 'Monthly Attendance',
            data: <?php echo json_encode($monthlyAttendance); ?>,
            backgroundColor: '#9cafaa',
            borderColor: '#8a9a97',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: '6-Month Attendance Trend',
                font: {
                    size: 14,
                    weight: 'bold'
                },
                color: '#3e3f5b'
            },
            legend: {
                labels: {
                    color: '#3e3f5b'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5,
                    color: '#3e3f5b'
                },
                grid: {
                    color: 'rgba(156, 175, 170, 0.2)'
                }
            },
            x: {
                ticks: {
                    color: '#3e3f5b'
                },
                grid: {
                    color: 'rgba(156, 175, 170, 0.2)'
                }
            }
        }
    }
});

// Attendance Comparison Chart
const comparisonCtx = document.getElementById('attendanceComparisonChart').getContext('2d');
new Chart(comparisonCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($comparisonMonths); ?>,
        datasets: [
            {
                label: 'Present',
                data: <?php echo json_encode($presentData); ?>,
                backgroundColor: '#4f6f52',
                borderColor: '#3e5840',
                borderWidth: 1
            },
            {
                label: 'Late',
                data: <?php echo json_encode($lateData); ?>,
                backgroundColor: '#f0a04b',
                borderColor: '#d08a3f',
                borderWidth: 1
            },
            {
                label: 'Absent',
                data: <?php echo json_encode($absentData); ?>,
                backgroundColor: '#d04848',
                borderColor: '#b03e3e',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Monthly Attendance Comparison (Present, Late, Absent)',
                font: {
                    size: 14,
                    weight: 'bold'
                },
                color: '#3e3f5b'
            },
            legend: {
                position: 'bottom',
                labels: {
                    color: '#3e3f5b',
                    boxWidth: 12,
                    padding: 15
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed.y + ' employees';
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: false,
                title: {
                    display: true,
                    text: 'Number of Employees',
                    color: '#3e3f5b'
                },
                ticks: {
                    stepSize: 5,
                    color: '#3e3f5b'
                },
                grid: {
                    color: 'rgba(156, 175, 170, 0.2)'
                }
            },
            x: {
                ticks: {
                    color: '#3e3f5b'
                },
                grid: {
                    color: 'rgba(156, 175, 170, 0.2)'
                }
            }
        }
    }
});

// Update time function
function updateTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
    var dateString = now.toLocaleDateString('en-US', { 
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('clock').textContent = timeString + ' | ' + dateString;
}

setInterval(updateTime, 1000);
updateTime();

// Animate on scroll effects
document.addEventListener('DOMContentLoaded', function() {
    const animateElements = document.querySelectorAll('.box, .employee-card, .quick-link, .chart-wrapper');
    
    // Add background elements
    document.querySelectorAll('.chart-wrapper').forEach(chart => {
        const bg = document.createElement('div');
        bg.className = 'chart-bg';
        chart.appendChild(bg);
    });
    
    document.querySelectorAll('.employee-card').forEach(card => {
        const bg = document.createElement('div');
        bg.className = 'card-bg';
        card.appendChild(bg);
    });
    
    document.querySelectorAll('.box').forEach(box => {
        const bg = document.createElement('div');
        bg.className = 'box-bg';
        box.appendChild(bg);
    });
    
    // Function to check if an element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.bottom >= 0
        );
    }
    
    // Function to handle scroll animation
    function handleScrollAnimation() {
        animateElements.forEach(element => {
            if (isInViewport(element) && !element.classList.contains('animated')) {
                element.classList.add('animated');
                element.style.animationPlayState = 'running';
            }
        });
    }
    
    // Initial check
    handleScrollAnimation();
    
    // Add scroll event listener
    window.addEventListener('scroll', handleScrollAnimation);
    
    // Add hover effects to buttons
    const buttons = document.querySelectorAll('.manual-attendance-btn, .btn-primary, .quick-link');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
});

// Handle iframe resizing
function sendHeightToParent() {
    const height = document.body.scrollHeight;
    
    if (window.parent) {
        window.parent.postMessage({
            type: 'resize',
            height: height
        }, '*');
    }
}


window.addEventListener('message', function(event) {
    if (event.data.type === 'requestHeight') {
        sendHeightToParent();
    }
});


window.addEventListener('load', sendHeightToParent);
window.addEventListener('resize', sendHeightToParent);


setInterval(sendHeightToParent, 2000);
</script>
</body>
</html>