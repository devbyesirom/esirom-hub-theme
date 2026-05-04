/**
 * Facebook Data Export Parser
 * Parses Facebook's official data export format
 */

class FacebookDataParser {
    constructor() {
        this.posts = [];
        this.photos = [];
        this.videos = [];
        this.profileInfo = {};
    }

    /**
     * Parse the entire Facebook export folder
     * @param {FileList} files - All files from the upload
     * @returns {Promise<Object>} Parsed data
     */
    async parseExport(files) {
        const fileMap = this.organizeFiles(files);
        
        console.log('📁 Facebook export files found:', Object.keys(fileMap).length);
        
        // Parse different data types
        await this.parsePosts(fileMap["this_profile's_activity_across_facebook/posts/profile_posts_1.json"]);
        await this.parseVideos(fileMap["this_profile's_activity_across_facebook/posts/videos.json"]);
        await this.parsePhotos(fileMap["this_profile's_activity_across_facebook/posts/uncategorized_photos.json"]);
        
        console.log('✅ Facebook parsing complete');
        console.log(`Found ${this.posts.length} posts, ${this.photos.length} photos, ${this.videos.length} videos`);
        
        return {
            posts: this.posts,
            photos: this.photos,
            videos: this.videos
        };
    }

    /**
     * Organize uploaded files into a map by relative path
     */
    organizeFiles(files) {
        const fileMap = {};
        for (let file of files) {
            // Extract relative path from webkitRelativePath
            const path = file.webkitRelativePath || file.name;
            const relativePath = path.split('/').slice(1).join('/'); // Remove root folder name
            fileMap[relativePath] = file;
        }
        return fileMap;
    }

    /**
     * Parse profile_posts_1.json
     */
    async parsePosts(file) {
        if (!file) {
            console.warn('No profile_posts_1.json found');
            return;
        }
        
        const data = await this.readJSON(file);
        if (!data || !Array.isArray(data)) {
            console.warn('Invalid posts data format');
            return;
        }

        console.log(`Parsing ${data.length} Facebook posts...`);

        data.forEach(item => {
            // Skip posts without content
            if (!item.timestamp) return;
            
            // Extract post text
            let postText = '';
            if (item.data && Array.isArray(item.data)) {
                const postData = item.data.find(d => d.post);
                if (postData) {
                    postText = postData.post;
                }
            }
            
            // Skip if no content
            if (!postText && !item.attachments) return;
            
            // Extract media (handle multiple media items for carousels)
            let mediaUri = null;
            let mediaType = 'text';
            let mediaCount = 0;
            let hasVideo = false;
            
            if (item.attachments && Array.isArray(item.attachments)) {
                for (const attachment of item.attachments) {
                    if (attachment.data && Array.isArray(attachment.data)) {
                        mediaCount = attachment.data.filter(d => d.media).length;
                        
                        for (const dataItem of attachment.data) {
                            if (dataItem.media) {
                                if (!mediaUri) {
                                    mediaUri = dataItem.media.uri; // Get first media item
                                }
                                if (dataItem.media.uri.includes('.mp4') || dataItem.media.uri.includes('.mov')) {
                                    hasVideo = true;
                                }
                            }
                        }
                    }
                }
            }
            
            // Determine content type
            let contentType = 'static';
            if (hasVideo) {
                contentType = 'reel'; // Facebook videos/reels
            } else if (mediaCount > 1) {
                contentType = 'carousel'; // Multiple photos
            } else if (mediaUri) {
                contentType = 'static'; // Single photo
            } else {
                contentType = 'text'; // Text only post
            }
            
            this.posts.push({
                id: 'fb_' + item.timestamp,
                scheduledDate: new Date(item.timestamp * 1000).toISOString().split('T')[0],
                platforms: ['facebook'],
                contentType: contentType,
                caption: this.decodeUnicode(postText),
                status: 'completed',
                mediaUri: mediaUri,
                mediaFile: mediaUri ? mediaUri.split('/').pop() : null,
                mediaFolder: mediaUri ? this.extractMediaFolder(mediaUri) : null,
                title: item.title || 'Facebook Post',
                // Placeholder - will be updated with client name
                finalContent: null
            });
        });

        console.log(`✅ Parsed ${this.posts.length} Facebook posts`);
    }

    /**
     * Parse videos.json
     */
    async parseVideos(file) {
        if (!file) return;
        
        const data = await this.readJSON(file);
        if (!data || !Array.isArray(data)) return;

        console.log(`Parsing ${data.length} Facebook videos...`);

        data.forEach(item => {
            if (!item.timestamp) return;
            
            let caption = '';
            if (item.data && Array.isArray(item.data)) {
                const postData = item.data.find(d => d.post);
                if (postData) {
                    caption = postData.post;
                }
            }
            
            let mediaUri = null;
            if (item.attachments && Array.isArray(item.attachments)) {
                for (const attachment of item.attachments) {
                    if (attachment.data && Array.isArray(attachment.data)) {
                        for (const dataItem of attachment.data) {
                            if (dataItem.media) {
                                mediaUri = dataItem.media.uri;
                                break;
                            }
                        }
                    }
                    if (mediaUri) break;
                }
            }
            
            this.videos.push({
                id: 'fb_video_' + item.timestamp,
                scheduledDate: new Date(item.timestamp * 1000).toISOString().split('T')[0],
                platforms: ['facebook'],
                contentType: 'reel',
                caption: this.decodeUnicode(caption),
                status: 'completed',
                mediaUri: mediaUri,
                mediaFile: mediaUri ? mediaUri.split('/').pop() : null,
                mediaFolder: mediaUri ? this.extractMediaFolder(mediaUri) : null,
                title: item.title || 'Facebook Video'
            });
        });

        console.log(`✅ Parsed ${this.videos.length} Facebook videos`);
    }

    /**
     * Parse uncategorized_photos.json
     */
    async parsePhotos(file) {
        if (!file) return;
        
        const data = await this.readJSON(file);
        if (!data || !Array.isArray(data)) return;

        console.log(`Parsing ${data.length} Facebook photos...`);

        data.forEach(item => {
            if (!item.timestamp) return;
            
            let caption = '';
            if (item.data && Array.isArray(item.data)) {
                const postData = item.data.find(d => d.post);
                if (postData) {
                    caption = postData.post;
                }
            }
            
            let mediaUri = null;
            if (item.attachments && Array.isArray(item.attachments)) {
                for (const attachment of item.attachments) {
                    if (attachment.data && Array.isArray(attachment.data)) {
                        for (const dataItem of attachment.data) {
                            if (dataItem.media) {
                                mediaUri = dataItem.media.uri;
                                break;
                            }
                        }
                    }
                    if (mediaUri) break;
                }
            }
            
            this.photos.push({
                uri: mediaUri,
                timestamp: item.timestamp,
                caption: this.decodeUnicode(caption),
                type: 'photo'
            });
        });

        console.log(`✅ Parsed ${this.photos.length} Facebook photos`);
    }

    /**
     * Extract media folder from URI
     * e.g., "this_profile's_activity_across_facebook/posts/media/Timelinephotos_ly1dL3KR1Q/926013359645625.jpg"
     * returns "Timelinephotos_ly1dL3KR1Q"
     */
    extractMediaFolder(uri) {
        if (!uri) return '';
        const parts = uri.split('/');
        // Find the folder after 'media'
        const mediaIndex = parts.indexOf('media');
        if (mediaIndex >= 0 && parts.length > mediaIndex + 1) {
            return parts[mediaIndex + 1];
        }
        return '';
    }

    /**
     * Read and parse JSON file
     */
    async readJSON(file) {
        if (!file) return null;
        
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    let content = e.target.result;
                    // Fix Unicode escape sequences for emojis
                    content = this.decodeUnicode(content);
                    const json = JSON.parse(content);
                    resolve(json);
                } catch (error) {
                    console.error('JSON parse error:', error);
                    resolve(null);
                }
            };
            reader.onerror = reject;
            reader.readAsText(file, 'UTF-8');
        });
    }

    /**
     * Decode Unicode escape sequences (for emojis)
     * Facebook uses format like \u00f0\u009f\u0094\u00a5 for 🔥
     */
    decodeUnicode(str) {
        try {
            // First, handle the escaped unicode sequences
            let decoded = str.replace(/\\u[\da-f]{4}/gi, (match) => {
                return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16));
            });
            
            // Then decode any URI encoded components
            try {
                decoded = decodeURIComponent(escape(decoded));
            } catch (e) {
                // If decoding fails, return the partially decoded string
                console.warn('Unicode decode warning:', e);
            }
            
            return decoded;
        } catch (error) {
            console.error('Unicode decode error:', error);
            return str;
        }
    }

    /**
     * Parse number from string (handles commas)
     */
    parseNumber(str) {
        if (!str) return 0;
        return parseInt(str.toString().replace(/,/g, '')) || 0;
    }

    /**
     * Generate posts without KPIs
     * Facebook doesn't provide insights in the export
     */
    generatePostsWithKPIs(clientId, clientName) {
        this.clientName = clientName || clientId;
        
        console.log('Generating Facebook posts (no KPIs - Facebook export does not include metrics)');
        
        // Combine all posts (from posts, videos, photos)
        const allPosts = [...this.posts];
        
        // Facebook doesn't provide insights in exports, so all KPIs are 0
        const postsWithKPIs = allPosts.map((post, index) => {
            const kpis = {
                facebook_reach: 0,
                facebook_impressions: 0,
                facebook_engagement: 0,
                facebook_likes: 0,
                facebook_comments: 0,
                facebook_shares: 0,
                facebook_saves: 0
            };
            
            // Video-specific metrics (also 0)
            if (post.contentType === 'reel') {
                kpis.facebook_views = 0;
                kpis.facebook_watch_time = 0;
                kpis.facebook_interactions = 0;
            }
            
            const finalPost = {
                ...post,
                clientId,
                kpis,
                createdAt: new Date().toISOString(),
                createdBy: 'import'
            };
            
            // Set client-specific media path
            if (post.mediaFile && post.mediaFolder) {
                const clientSlug = this.clientName.toLowerCase().replace(/\s+/g, '-');
                const mediaType = post.contentType === 'reel' ? 'videos' : 'photos';
                finalPost.finalContent = `/wp-content/media/${clientSlug}/facebook/${mediaType}/${post.mediaFolder}/${post.mediaFile}`;
                finalPost.mediaPath = `media/${clientSlug}/facebook/${mediaType}/${post.mediaFolder}`;
            }
            
            return finalPost;
        });

        console.log('Generated Facebook posts:', postsWithKPIs);
        console.log('⚠️  Note: Facebook exports do not include metrics. KPIs are set to 0. Connect Facebook Graph API for real-time metrics.');
        
        return postsWithKPIs;
    }
}

// Export for use in the admin panel
if (typeof window !== 'undefined') {
    window.FacebookDataParser = FacebookDataParser;
}
