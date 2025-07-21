<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Conservation Hub</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .incentives-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 2.5rem;
            color: #2c5282;
            margin-bottom: 0.5rem;
        }
        .header p {
            font-size: 1.1rem;
            color: #4a5568;
        }
        .gamification-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .leaderboard, .challenges {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.5rem;
            color: #2c5282;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .leaderboard-list {
            list-style: none;
            padding: 0;
        }
        .leaderboard-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .leaderboard-item:last-child {
            border-bottom: none;
        }
        .user-rank {
            font-weight: bold;
            color: #2c5282;
        }
        .user-points {
            background: #bee3f8;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: bold;
        }
        .challenge-card {
            background: #f7fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #4299e1;
        }
        .challenge-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
        }
        .challenge-progress {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            margin: 0.5rem 0;
        }
        .progress-bar {
            height: 100%;
            background: #4299e1;
            border-radius: 4px;
            width: 30%;
        }
        .badges-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .badges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .badge {
            text-align: center;
        }
        .badge-icon {
            width: 80px;
            height: 80px;
            background: #ebf8ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 2rem;
            color: #2b6cb0;
        }
        .badge-name {
            font-size: 0.9rem;
            font-weight: 500;
        }
        .earned {
            background: #bee3f8;
            border: 2px solid #3182ce;
        }
        .tips-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .tip-card {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .tip-icon {
            font-size: 1.5rem;
            color: #3182ce;
        }
        .tip-content h3 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #3182ce;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #2c5282;
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 2rem;
        }
        .reward-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #48bb78;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
@extends('app1')

@section('content')
<div class="incentives-container">
    <div class="header">
        <h1><i class="fas fa-trophy"></i> Water Conservation Hub</h1>
        <p>Earn rewards and compete with others by saving water!</p>
    </div>

    <div class="gamification-section">
        <div class="leaderboard">
            <h2 class="section-title"><i class="fas fa-crown"></i> Community Leaderboard</h2>
            <ul class="leaderboard-list">
                <li class="leaderboard-item">
                    <span class="user-rank">1. You <i class="fas fa-user"></i></span>
                    <span class="user-points">1,250 pts</span>
                </li>
                <li class="leaderboard-item">
                    <span class="user-rank">2. GreenFamily</span>
                    <span class="user-points">1,100 pts</span>
                </li>
                <li class="leaderboard-item">
                    <span class="user-rank">3. EcoWarrior</span>
                    <span class="user-points">980 pts</span>
                </li>
                <li class="leaderboard-item">
                    <span class="user-rank">4. WaterSaver</span>
                    <span class="user-points">850 pts</span>
                </li>
                <li class="leaderboard-item">
                    <span class="user-rank">5. NatureLover</span>
                    <span class="user-points">720 pts</span>
                </li>
            </ul>
        </div>

        <div class="challenges">
            <h2 class="section-title"><i class="fas fa-tasks"></i> Current Challenges</h2>
            <div class="challenge-card">
                <div class="challenge-title">
                    <span>Weekly Water Reduction</span>
                    <span>150/500 pts</span>
                </div>
                <p>Reduce your water usage by 10% this week</p>
                <div class="challenge-progress">
                    <div class="progress-bar" style="width: 30%"></div>
                </div>
                <button class="btn" style="margin-top: 0.5rem; padding: 0.5rem 1rem;">Join Challenge</button>
            </div>
            <div class="challenge-card">
                <div class="challenge-title">
                    <span>Fix-a-Leak Week</span>
                    <span>0/300 pts</span>
                </div>
                <p>Identify and fix any leaks in your home</p>
                <div class="challenge-progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <button class="btn" style="margin-top: 0.5rem; padding: 0.5rem 1rem;">Join Challenge</button>
            </div>
            <div class="challenge-card">
                <div class="challenge-title">
                    <span>30-Day Streak</span>
                    <span>7/30 days</span>
                </div>
                <p>Log your water usage for 30 consecutive days</p>
                <div class="challenge-progress">
                    <div class="progress-bar" style="width: 23%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="badges-section">
        <h2 class="section-title"><i class="fas fa-medal"></i> Your Badges</h2>
        <p>Collect badges by completing challenges and saving water!</p>
        <div class="badges-grid">
            <div class="badge earned">
                <div class="badge-icon"><i class="fas fa-leaf"></i></div>
                <div class="badge-name">Eco Starter</div>
            </div>
            <div class="badge earned">
                <div class="badge-icon"><i class="fas fa-tint"></i></div>
                <div class="badge-name">Water Saver</div>
            </div>
            <div class="badge">
                <div class="badge-icon"><i class="fas fa-bolt"></i></div>
                <div class="badge-name">Power User</div>
            </div>
            <div class="badge">
                <div class="badge-icon"><i class="fas fa-star"></i></div>
                <div class="badge-name">Conservationist</div>
            </div>
            <div class="badge">
                <div class="badge-icon"><i class="fas fa-trophy"></i></div>
                <div class="badge-name">Water Hero</div>
            </div>
        </div>
    </div>

    <div class="tips-section">
        <h2 class="section-title"><i class="fas fa-lightbulb"></i> Water-Saving Tips</h2>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-shower"></i></div>
            <div class="tip-content">
                <h3>Shorter Showers</h3>
                <p>Reduce your shower time by 2 minutes to save up to 10 litres of water.</p>
            </div>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-utensils"></i></div>
            <div class="tip-content">
                <h3>Efficient Dishwashing</h3>
                <p>Run your dishwasher only when full to save water and energy.</p>
            </div>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-faucet"></i></div>
            <div class="tip-content">
                <h3>Fix Leaks</h3>
                <p>A dripping faucet can waste 20 litres of water per day. Fix leaks promptly!</p>
            </div>
        </div>
    </div>

    <a href="{{ url('/') }}" class="btn btn-back">Back to Dashboard</a>
</div>

<div class="reward-notification" id="rewardNotification">
    <i class="fas fa-gift"></i> Congratulations! You've earned 50 points for saving water today!
</div>

<script>
    // Simulate earning a reward (in a real app, this would come from backend)
    setTimeout(() => {
        document.getElementById('rewardNotification').style.display = 'block';
        setTimeout(() => {
            document.getElementById('rewardNotification').style.display = 'none';
        }, 5000);
    }, 3000);

    // Challenge join functionality
    document.querySelectorAll('.challenge-card .btn').forEach(button => {
        button.addEventListener('click', function() {
            this.textContent = 'Joined!';
            this.style.backgroundColor = '#48bb78';
            this.disabled = true;
        });
    });
</script>
@endsection
</body>
</html>
