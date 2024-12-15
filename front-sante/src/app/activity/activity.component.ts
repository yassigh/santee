
import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';
import { ActivityService } from '../activity.service';

@Component({
  selector: 'app-activity',
  templateUrl: './activity.component.html',
  styleUrls: ['./activity.component.css']
})
export class ActivityComponent implements OnInit {
  age: number = 0;
  selectedActivity: string = '';
  time: number = 0;
  activities: any[] = [];
  goalReached: boolean = false;
  currentDate: Date = new Date();
  selectedPeriod: string = 'jour';

  userId: number | null = null;

  constructor(
    private activityService: ActivityService,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
   
    this.userId = this.authService.getUserIdFromToken();

   
    if (this.userId) {
     
      this.activityService.getActivitiesByUser(this.userId).subscribe(
        (data) => {
          this.activities = data;
        },
        (error) => {
          console.error('Error fetching activities', error);
        }
      );
    } else {
      console.error('User ID not found in token');
    }
  }
  checkGoal(): void {
    if (this.time >= 2 && this.selectedActivity.toLowerCase() !== 'exercice') {
      this.goalReached = true;
    } else {
      this.goalReached = false;
    }
  }

  submitActivity(): void {
    
    if (!this.userId) {
      alert('User ID not found. Please login again.');
      return;
    }

    const payload = {
      activity: this.selectedActivity,
      heure: this.time,
      age: this.age,
      user_id: this.userId 
    };

    this.activityService.addActivity(payload).subscribe(
      (response) => {
        console.log('Activity added successfully', response);
        alert('Activity added successfully');
      },
      (error) => {
        console.error('Error adding activity', error);
        alert('Error adding activity');
      }
    );
  }
}
