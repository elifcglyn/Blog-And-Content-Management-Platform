export const mockUsers = [
  {
    id: "1",
    name: "Sarah Chen",
    username: "sarahchen",
    avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop",
    bio: "Full-stack developer passionate about clean code and user experience",
    posts: 15,
    followers: 350,
    totalViews: 12000,
  },
  {
    id: "2",
    name: "Alex Morgan",
    username: "alexmorgan",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop",
    bio: "Tech enthusiast and writer",
    posts: 8,
    followers: 120,
    totalViews: 5000,
  },
  {
    id: "3",
    name: "Elif Yilmaz",
    username: "elifyilmaz",
    avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop",
    bio: "UX Designer & Frontend Developer",
    posts: 22,
    followers: 580,
    totalViews: 18000,
  },
];

export const mockPosts = [
  {
    id: "1",
    title: "Understanding React Hooks: A Complete Guide",
    slug: "understanding-react-hooks-complete-guide",
    excerpt: "Deep dive into React Hooks and how they revolutionized the way we write React components. Learn about useState, useEffect, and custom hooks.",
    content: `# Understanding React Hooks: A Complete Guide

React Hooks have fundamentally changed how we write React components. In this comprehensive guide, we'll explore the most important hooks and how to use them effectively.

## What are React Hooks?

Hooks are functions that let you "hook into" React state and lifecycle features from function components. They were introduced in React 16.8 and have since become the standard way to write React components.

## useState Hook

The useState hook is the most basic hook for managing state in functional components:

\`\`\`javascript
const [count, setCount] = useState(0);

function increment() {
  setCount(count + 1);
}
\`\`\`

## useEffect Hook

The useEffect hook lets you perform side effects in function components:

\`\`\`javascript
useEffect(() => {
  document.title = \`Count: \${count}\`;
}, [count]);
\`\`\`

## Custom Hooks

You can create your own hooks to reuse stateful logic:

\`\`\`javascript
function useLocalStorage(key, initialValue) {
  const [value, setValue] = useState(() => {
    const stored = localStorage.getItem(key);
    return stored ? JSON.parse(stored) : initialValue;
  });

  useEffect(() => {
    localStorage.setItem(key, JSON.stringify(value));
  }, [key, value]);

  return [value, setValue];
}
\`\`\`

## Conclusion

React Hooks provide a more direct API to the React concepts you already know. They enable better code reuse and composition.`,
    category: "React",
    authorId: "1",
    author: mockUsers[0],
    coverImage: "https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=1200&h=600&fit=crop",
    publishedAt: "2026-03-15T10:00:00Z",
    updatedAt: "2026-03-17T14:30:00Z",
    readingTime: 8,
    views: 1245,
    comments: 23,
    status: "published",
    versions: [
      {
        version: 4,
        authorId: "1",
        author: mockUsers[0],
        date: "2026-03-17T14:30:00Z",
        changeNote: "Added custom hooks section",
        isCurrent: true,
      },
      {
        version: 3,
        authorId: "1",
        author: mockUsers[0],
        date: "2026-03-16T09:15:00Z",
        changeNote: "Fixed code examples and typos",
        isCurrent: false,
      },
      {
        version: 2,
        authorId: "1",
        author: mockUsers[0],
        date: "2026-03-15T16:20:00Z",
        changeNote: "Added useEffect section",
        isCurrent: false,
      },
      {
        version: 1,
        authorId: "1",
        author: mockUsers[0],
        date: "2026-03-15T10:00:00Z",
        changeNote: "Initial draft",
        isCurrent: false,
      },
    ],
  },
  {
    id: "2",
    title: "Building Scalable Design Systems with Tailwind CSS",
    slug: "building-scalable-design-systems-tailwind",
    excerpt: "Learn how to create and maintain a consistent design system using Tailwind CSS and modern component libraries.",
    content: "Full article content here...",
    category: "Design",
    authorId: "2",
    author: mockUsers[1],
    coverImage: "https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&h=600&fit=crop",
    publishedAt: "2026-03-14T08:00:00Z",
    updatedAt: "2026-03-14T08:00:00Z",
    readingTime: 12,
    views: 892,
    comments: 15,
    status: "published",
    versions: [
      {
        version: 1,
        authorId: "2",
        author: mockUsers[1],
        date: "2026-03-14T08:00:00Z",
        changeNote: "Initial publication",
        isCurrent: true,
      },
    ],
  },
  {
    id: "3",
    title: "TypeScript Best Practices for 2026",
    slug: "typescript-best-practices-2026",
    excerpt: "Discover the latest TypeScript patterns and practices that will make your code more maintainable and type-safe.",
    content: "Full article content here...",
    category: "TypeScript",
    authorId: "3",
    author: mockUsers[2],
    coverImage: "https://images.unsplash.com/photo-1516116216624-53e697fedbea?w=1200&h=600&fit=crop",
    publishedAt: "2026-03-13T12:00:00Z",
    updatedAt: "2026-03-18T10:00:00Z",
    readingTime: 10,
    views: 2341,
    comments: 45,
    status: "published",
    versions: [
      {
        version: 3,
        authorId: "3",
        author: mockUsers[2],
        date: "2026-03-18T10:00:00Z",
        changeNote: "Updated for TypeScript 5.4",
        isCurrent: true,
      },
      {
        version: 2,
        authorId: "3",
        author: mockUsers[2],
        date: "2026-03-15T11:00:00Z",
        changeNote: "Added utility types section",
        isCurrent: false,
      },
      {
        version: 1,
        authorId: "3",
        author: mockUsers[2],
        date: "2026-03-13T12:00:00Z",
        changeNote: "Initial publication",
        isCurrent: false,
      },
    ],
  },
  {
    id: "4",
    title: "Mastering CSS Grid Layout",
    slug: "mastering-css-grid-layout",
    excerpt: "A comprehensive guide to CSS Grid, from basics to advanced techniques for modern web layouts.",
    content: "Full article content here...",
    category: "CSS",
    authorId: "1",
    author: mockUsers[0],
    coverImage: "https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&h=600&fit=crop",
    publishedAt: "2026-03-12T14:00:00Z",
    updatedAt: "2026-03-12T14:00:00Z",
    readingTime: 15,
    views: 1567,
    comments: 28,
    status: "published",
    versions: [
      {
        version: 1,
        authorId: "1",
        author: mockUsers[0],
        date: "2026-03-12T14:00:00Z",
        changeNote: "Initial publication",
        isCurrent: true,
      },
    ],
  },
  {
    id: "5",
    title: "State Management in Modern React Applications",
    slug: "state-management-modern-react",
    excerpt: "Exploring different state management solutions: Context API, Zustand, Redux Toolkit, and when to use each.",
    content: "Full article content here...",
    category: "React",
    authorId: "2",
    author: mockUsers[1],
    coverImage: "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200&h=600&fit=crop",
    publishedAt: "2026-03-10T09:00:00Z",
    updatedAt: "2026-03-10T09:00:00Z",
    readingTime: 20,
    views: 3201,
    comments: 67,
    status: "published",
    versions: [
      {
        version: 2,
        authorId: "2",
        author: mockUsers[1],
        date: "2026-03-11T15:00:00Z",
        changeNote: "Added Zustand examples",
        isCurrent: true,
      },
      {
        version: 1,
        authorId: "2",
        author: mockUsers[1],
        date: "2026-03-10T09:00:00Z",
        changeNote: "Initial publication",
        isCurrent: false,
      },
    ],
  },
  {
    id: "6",
    title: "Web Performance Optimization Techniques",
    slug: "web-performance-optimization",
    excerpt: "Learn how to dramatically improve your website's performance with modern optimization techniques and tools.",
    content: "Full article content here...",
    category: "Performance",
    authorId: "3",
    author: mockUsers[2],
    coverImage: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&h=600&fit=crop",
    publishedAt: "2026-03-08T11:00:00Z",
    updatedAt: "2026-03-08T11:00:00Z",
    readingTime: 18,
    views: 2890,
    comments: 41,
    status: "published",
    versions: [
      {
        version: 1,
        authorId: "3",
        author: mockUsers[2],
        date: "2026-03-08T11:00:00Z",
        changeNote: "Initial publication",
        isCurrent: true,
      },
    ],
  },
];

export const mockNotifications = [
  {
    id: "1",
    type: "like",
    message: "Elif liked your post 'Understanding React Hooks'",
    postId: "1",
    actorId: "3",
    actor: mockUsers[2],
    timestamp: "2026-03-19T08:30:00Z",
    read: false,
  },
  {
    id: "2",
    type: "comment",
    message: "Alex commented on 'Understanding React Hooks'",
    postId: "1",
    actorId: "2",
    actor: mockUsers[1],
    timestamp: "2026-03-18T16:45:00Z",
    read: false,
  },
  {
    id: "3",
    type: "follow",
    message: "Elif started following you",
    actorId: "3",
    actor: mockUsers[2],
    timestamp: "2026-03-18T12:20:00Z",
    read: true,
  },
  {
    id: "4",
    type: "version",
    message: "Version 4 of 'Understanding React Hooks' was published",
    postId: "1",
    timestamp: "2026-03-17T14:30:00Z",
    read: true,
  },
  {
    id: "5",
    type: "like",
    message: "Alex liked your post 'Mastering CSS Grid Layout'",
    postId: "4",
    actorId: "2",
    actor: mockUsers[1],
    timestamp: "2026-03-17T10:15:00Z",
    read: true,
  },
  {
    id: "6",
    type: "comment",
    message: "Elif commented on 'Mastering CSS Grid Layout'",
    postId: "4",
    actorId: "3",
    actor: mockUsers[2],
    timestamp: "2026-03-16T14:00:00Z",
    read: true,
  },
];

export const mockComments = [
  {
    id: "1",
    postId: "1",
    authorId: "2",
    author: mockUsers[1],
    content: "Great article! The custom hooks section was particularly helpful.",
    timestamp: "2026-03-18T16:45:00Z",
  },
  {
    id: "2",
    postId: "1",
    authorId: "3",
    author: mockUsers[2],
    content: "This is exactly what I needed to understand hooks better. Thanks for sharing!",
    timestamp: "2026-03-17T09:30:00Z",
  },
  {
    id: "3",
    postId: "1",
    authorId: "1",
    author: mockUsers[0],
    content: "Thanks everyone! I'm glad this was helpful.",
    timestamp: "2026-03-18T18:00:00Z",
  },
];

export const mockTopics = [
  "React",
  "TypeScript",
  "Design",
  "CSS",
  "Performance",
  "JavaScript",
  "Web Development",
  "UI/UX",
];

export const currentUser = mockUsers[0];
