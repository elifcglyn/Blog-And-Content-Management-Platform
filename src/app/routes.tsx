import { createBrowserRouter, Navigate } from "react-router";
import { Layout } from "./layout"; 
import { HomePage } from "./pages/home";
import { AuthPage } from "./pages/auth";
import { NewPostPage } from "./pages/new-post"; 
import { PostDetailPage } from "./pages/post-detail";
import { ProfilePage } from "./pages/profile"; 
import { NotificationsPage } from "./pages/notifications";
import { SettingsPage } from "./pages/settings";
import { AnalyticsPage } from "./pages/analytics"; // 📊 Import edildi
import { BookmarksPage } from "./pages/bookmarks";   // 🔖 Import edildi
import { WrappedPage } from "./pages/wrapped";

// Route tanımlarına ekle:

export const router = createBrowserRouter([
  {
    path: "/auth",
    element: <AuthPage />,
  },
  {
    path: "/",
    element: <Layout />, // 💡 Sidebar ve Header bu Layout'un içinde olduğu için tüm alt sayfalarda görünecek!
    children: [
      {
        index: true, 
        element: <HomePage />,
      },
      
    
      {
        path: "write",
        element: <NewPostPage />,
      },
      {
        path: "post/:slug", 
        element: <PostDetailPage />,
      },
      {
        path: "profile",
        element: <ProfilePage />,
      },
      {
        path: "notifications",
        element: <NotificationsPage />,
      },
      {
        path: "settings",
        element: <SettingsPage />,
      },
      // 🚀 YENİ ÖZELLİKLER ARTIK AKTİF!
      {
        path: "analytics",
        element: <AnalyticsPage />,
      },
      { path: "/wrapped", element: <WrappedPage /> },
      {
        path: "bookmarks",
        element: <BookmarksPage />,
      }
    ],
  },
  {
    path: "*",
    element: <Navigate to="/" replace />,
  },
]);