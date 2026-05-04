# Content Calendar System Specification

## Overview
Complete content approval workflow system with concept review, final content upload, client approval, and KPI tracking.

## User Roles & Permissions

### Brand Reps & Admins
- Create new posts
- Upload content concepts (optional)
- Upload final content
- Write captions (optional but encouraged)
- Mark posts as "Posted"
- Add post KPIs after publishing
- View all post statuses

### Clients
- View content concepts
- View final content
- Approve posts
- Request reviews with feedback
- Edit captions directly
- Suggest caption changes
- View post status

## Post Workflow States

1. **Draft** - Initial creation
2. **Concept Review** (Optional) - Client reviews concept image/video
3. **Concept Approved** - Client approved concept, awaiting final content
4. **Pending Approval** - Final content uploaded, awaiting client approval
5. **Needs Review** - Client requested changes
6. **Approved** - Client approved, ready to post
7. **Posted** - Brand rep confirmed post is live
8. **Completed** - KPIs added, post fully tracked

## Content Types & Specifications

### Static Images
- **Format:** 1080x1080 (Square)
- **Platforms:** Instagram, Facebook, LinkedIn
- **File types:** JPG, PNG

### Videos
- **Format:** 1920x1080 (Landscape)
- **Platforms:** YouTube, Facebook
- **File types:** MP4, MOV
- **Max size:** 100MB

### Reels/Stories
- **Format:** 1080x1920 (Vertical)
- **Platforms:** Instagram Reels, Facebook Reels, TikTok
- **File types:** MP4, MOV
- **Max size:** 50MB

## Data Structure

```javascript
{
  post: {
    id: "unique_id",
    clientId: "client_id",
    createdBy: "user_id",
    createdAt: "2025-10-01T10:00:00Z",
    
    // Content
    platform: "instagram", // facebook, linkedin, youtube, tiktok, x
    contentType: "reel", // static, video, reel
    scheduledDate: "2025-10-15",
    caption: "Post caption text...",
    
    // Concept phase (optional)
    conceptImage: "url_to_concept",
    conceptApproved: false,
    conceptFeedback: "",
    
    // Final content
    finalContent: "url_to_final_asset",
    finalContentType: "image/video",
    dimensions: "1080x1920",
    
    // Approval workflow
    status: "pending_approval", // draft, concept_review, concept_approved, pending_approval, needs_review, approved, posted, completed
    clientFeedback: "",
    captionSuggestion: "",
    approvedAt: null,
    approvedBy: null,
    
    // Publishing
    postedAt: null,
    postedBy: null,
    postUrl: "",
    
    // KPIs (added after posting)
    kpis: {
      reach: 0,
      impressions: 0,
      engagement: 0,
      likes: 0,
      comments: 0,
      shares: 0,
      saves: 0,
      clicks: 0,
      videoViews: 0,
      watchTime: 0
    }
  }
}
```

## UI Components

### 1. Calendar Grid View
- Month/Week view toggle
- Color-coded by status
- Filter by platform
- Filter by status
- Search posts

### 2. Create Post Modal
- Platform selector
- Content type selector
- Scheduled date picker
- Caption editor (rich text)
- **Step 1:** Upload concept (optional, can skip)
- **Step 2:** Upload final content
- Dimension validator
- File size validator

### 3. Post Card
- Thumbnail preview
- Platform icon
- Status badge
- Scheduled date
- Caption preview
- Action buttons based on role & status

### 4. Client Review Modal
- Full content preview
- Caption display/edit
- Approve button
- Request Review button
- Feedback textarea
- Caption suggestion field

### 5. Brand Rep Actions Modal
- View client feedback
- Upload revised content
- Mark as Posted
- Add post URL
- Add KPIs form

### 6. KPI Entry Form
- Platform-specific metrics
- Reach, Impressions, Engagement
- Likes, Comments, Shares, Saves
- Video-specific: Views, Watch Time
- Link clicks
- Save & auto-feed to reports

## Integration Points

### With Reporting System
- Top performing content automatically pulled from calendar
- KPIs aggregate to monthly totals
- Content type analysis (static vs video vs reel)
- Platform performance comparison

### With Dashboard
- Pending approvals count alert
- Recent posts widget
- Upcoming scheduled posts

## Storage
- Posts: `client_posts_{clientId}` in localStorage
- Format: Array of post objects
- Auto-sync when data changes

## Notifications (Future Enhancement)
- Email when post needs approval
- Email when feedback provided
- Email when post is live
- In-app notification badges

## Implementation Priority

### Phase 1 (Current)
1. Calendar grid view
2. Create post modal
3. Post cards with status
4. Client approval workflow
5. Brand rep posting confirmation

### Phase 2
6. KPI entry system
7. Integration with reports
8. Advanced filters
9. Bulk actions

### Phase 3
10. Direct social media posting API
11. Automated KPI fetching
12. Analytics dashboard
13. Content library/reuse

## File Upload Handling
- Use FileReader API for preview
- Store as base64 in localStorage (demo)
- Production: Upload to server/cloud storage
- Validate dimensions before upload
- Compress images if needed

## Caption Editor Features
- Character counter (platform-specific limits)
- Hashtag suggestions
- Emoji picker
- Mention support (@username)
- Link shortening
- Preview formatting

## Status Badge Colors
- Draft: Gray
- Concept Review: Blue
- Concept Approved: Light Blue
- Pending Approval: Yellow
- Needs Review: Orange
- Approved: Green
- Posted: Purple
- Completed: Dark Green

## Next Steps
1. Implement calendar grid
2. Build post creation flow
3. Add approval workflow
4. Integrate KPI tracking
5. Connect to reporting
