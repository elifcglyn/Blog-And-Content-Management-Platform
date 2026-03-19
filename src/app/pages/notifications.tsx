import * as React from "react";
import { Link } from "react-router";

import { Avatar, AvatarFallback, AvatarImage } from "../components/ui/avatar";
import { Button } from "../components/ui/button";
import { 
  Bell, Heart, MessageSquare, GitBranch, 
  UserPlus, Star, ArrowRight, CheckCircle2, Trash2 
} from "lucide-react";

// Başlangıç verilerini (Mock) bir değişkene alalım
const initialNotifications = [
  { id: 1, type: "like", user: { name: "Sarah Chen", avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=150" }, post: { title: "React Compiler: Geleceğe Bakış", slug: "react-compiler" }, timestamp: "5 dk önce", isRead: false },
  { id: 2, type: "comment", user: { name: "Alex Johnson", avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150" }, post: { title: "Tailwind v4", slug: "tailwind-v4" }, commentPreview: "Harika bir yazı Elif!", timestamp: "23 dk önce", isRead: false },
  { id: 3, type: "version", post: { title: "Postify v2.5 Yayında!", slug: "postify-v2-5" }, timestamp: "1 sa önce", isRead: true },
  { id: 4, type: "follow", user: { name: "Maria Garcia", avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=150" }, timestamp: "2 sa önce", isRead: true }
];

export function NotificationsPage() {
  // 🚀 GERÇEKÇİ STATE YÖNETİMİ
  const [notifications, setNotifications] = React.useState(initialNotifications);
  const [activeFilter, setActiveFilter] = React.useState("all");

  // 🌍 BACKEND BAĞLANTISI HAZIRLIĞI (Arkadaşın bitirince burayı açacaksın)
  /*
  React.useEffect(() => {
    fetch('https://api.postify.com/notifications')
      .then(res => res.json())
      .then(data => setNotifications(data));
  }, []);
  */

  // Okunmamış sayısını anlık hesapla
  const unreadCount = notifications.filter(n => !n.isRead).length;

  // 🛠️ AKSİYONLAR
  const markAsRead = (id: number) => {
    setNotifications(notifications.map(n => n.id === id ? { ...n, isRead: true } : n));
  };

  const markAllAsRead = () => {
    setNotifications(notifications.map(n => ({ ...n, isRead: true })));
  };

  const deleteNotification = (id: number) => {
    setNotifications(notifications.filter(n => n.id !== id));
  };

  const getIconConfig = (type: string) => {
    switch (type) {
      case "like": return { icon: Heart, color: "text-red-500 bg-red-50" };
      case "comment": return { icon: MessageSquare, color: "text-sky-500 bg-sky-50" };
      case "version": return { icon: GitBranch, color: "text-teal-600 bg-teal-50" };
      case "follow": return { icon: UserPlus, color: "text-blue-600 bg-blue-50" };
      default: return { icon: Bell, color: "text-slate-500 bg-slate-50" };
    }
  };

  return (
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans overflow-auto animate-in fade-in duration-500">
     
      
      <main className="flex-1 container mx-auto px-6 py-12 max-w-4xl">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 pb-8 border-b border-slate-100">
          <div className="space-y-2">
            <h1 className="font-serif text-5xl italic tracking-tighter text-slate-900 dark:text-white">Notifications</h1>
            <p className="text-slate-500 italic">Etkileşimleri ve güncellemeleri yönet.</p>
          </div>
          
          <div className="flex items-center gap-4">
             <div className="bg-teal-50 px-6 py-3 rounded-2xl border border-teal-100">
                <span className="text-2xl font-bold text-teal-700">{unreadCount}</span>
                <span className="ml-2 text-xs uppercase font-bold tracking-widest text-teal-600/60">New</span>
             </div>
             <Button onClick={markAllAsRead} variant="outline" className="rounded-full gap-2 border-slate-200">
                <CheckCircle2 className="w-4 h-4" /> Mark all read
             </Button>
          </div>
        </div>

        {/* FİLTRELER */}
        <div className="flex gap-2 mb-8">
            {["all", "unread"].map(f => (
                <button 
                    key={f}
                    onClick={() => setActiveFilter(f)}
                    className={`px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all ${activeFilter === f ? "bg-slate-900 text-white shadow-lg" : "text-slate-400 hover:bg-slate-50"}`}
                >
                    {f === "all" ? "Tümü" : "Okunmamış"}
                </button>
            ))}
        </div>

        {/* BİLDİRİM LİSTESİ */}
        <div className="space-y-3">
          {notifications
            .filter(n => activeFilter === "unread" ? !n.isRead : true)
            .map((notif) => {
                const config = getIconConfig(notif.type);
                return (
                    <div 
                        key={notif.id}
                        onClick={() => markAsRead(notif.id)}
                        className={`group flex items-center gap-6 p-6 rounded-[2rem] border transition-all cursor-pointer ${notif.isRead ? "bg-white border-slate-50 opacity-70" : "bg-teal-50/30 border-teal-100 shadow-sm shadow-teal-500/5"}`}
                    >
                        <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 ${config.color}`}>
                            <config.icon className="w-6 h-6" />
                        </div>

                        <div className="flex-1 min-w-0">
                            <div className="flex items-center gap-2 mb-1">
                                {notif.user && (
                                    <Avatar className="w-5 h-5">
                                        <AvatarImage src={notif.user.avatar} />
                                        <AvatarFallback>{notif.user.name[0]}</AvatarFallback>
                                    </Avatar>
                                )}
                                <p className="text-sm text-slate-900 dark:text-white truncate">
                                    <span className="font-bold">{notif.user?.name || "System"}</span> 
                                    {" "}{notif.type === 'like' ? 'yazını beğendi' : notif.type === 'comment' ? 'yorum yaptı' : 'bir güncelleme var'}
                                </p>
                            </div>
                            {notif.post && <p className="text-xs text-teal-600 font-medium mb-1 truncate">"{notif.post.title}"</p>}
                            <p className="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">{notif.timestamp}</p>
                        </div>

                        <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                onClick={(e) => { e.stopPropagation(); deleteNotification(notif.id); }}
                                className="p-2 hover:bg-red-50 hover:text-red-500 rounded-full transition-colors"
                            >
                                <Trash2 className="w-4 h-4" />
                            </button>
                            <div className="w-10 h-10 bg-slate-900 text-white rounded-full flex items-center justify-center shadow-lg">
                                <ArrowRight className="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                );
            })}
        </div>
      </main>
    </div>
  );
}