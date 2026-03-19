import { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router";
import { 
  Home, 
  FileText, 
  PlusCircle, 
  Bell, 
  Settings, 
  LogOut, 
  ChevronLeft, 
  ChevronRight,
  BarChart3, Bookmark,
  Sparkles,
  User,
  Menu
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Button } from "./ui/button";
import { currentUser } from "../data/mock-data";
import { motion, AnimatePresence } from "framer-motion";

const navItems = [
  { icon: Home, label: "Ana Sayfa", path: "/" },
  { icon: FileText, label: "Yazılarım", path: "/profile" },
  { icon: PlusCircle, label: "Yeni Yazı", path: "/write" },
  { icon: Bell, label: "Bildirimler", path: "/notifications" },
  { icon: Settings, label: "Ayarlar", path: "/settings" },
  { icon: BarChart3, label: "İstatistikler", path: "/analytics" }, // 📊 YENİ!
  { icon: Bookmark, label: "Kaydedilenler", path: "/bookmarks" },
];

export function CollapsibleSidebar() {
  const [isCollapsed, setIsCollapsed] = useState(false);
  const location = useLocation();
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem("isAuthenticated");
    localStorage.removeItem("userName");
    navigate("/auth");
  };

  return (
    <motion.aside
      animate={{ width: isCollapsed ? 88 : 280 }}
      transition={{ duration: 0.4, ease: [0.23, 1, 0.32, 1] }}
      className="h-screen border-r border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-950 flex flex-col relative z-50 shadow-sm"
    >
      {/* LOGO BÖLÜMÜ */}
      <div className={`p-6 mb-4 ${isCollapsed ? "flex justify-center" : ""}`}>
        <Link to="/" className="flex items-center gap-3 group">
          <div className="w-10 h-10 bg-slate-950 dark:bg-white rounded-2xl flex items-center justify-center shrink-0 transition-all group-hover:rotate-12 group-hover:shadow-lg group-hover:shadow-teal-500/20">
            <Sparkles className="w-6 h-6 text-teal-400 dark:text-teal-600 fill-current" />
          </div>
          
          <AnimatePresence>
            {!isCollapsed && (
              <motion.span
                initial={{ opacity: 0, x: -10 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -10 }}
                className="font-serif text-3xl italic tracking-tighter text-slate-950 dark:text-white"
              >
                Postify
              </motion.span>
            )}
          </AnimatePresence>
        </Link>
      </div>

      {/* Navigasyon Linkleri */}
      <nav className="flex-1 py-4 overflow-y-auto scrollbar-hide">
        <ul className="space-y-2 px-4">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = location.pathname === item.path;
            
            return (
              <li key={item.path}>
                <Link to={item.path}>
                  <Button
                    variant="ghost"
                    className={`w-full group relative rounded-2xl p-3 h-12 transition-all ${
                      isCollapsed ? "justify-center" : "justify-start gap-4"
                    } ${
                      isActive 
                        ? "bg-teal-50 text-teal-700 font-bold shadow-sm shadow-teal-500/5 dark:bg-teal-950/30 dark:text-teal-400" 
                        : "text-slate-500 hover:bg-slate-50 hover:text-teal-600"
                    }`}
                  >
                    <Icon className={`h-5 w-5 flex-shrink-0 transition-transform ${isActive ? "scale-110" : "group-hover:scale-110"}`} />
                    {!isCollapsed && <span className="tracking-tight">{item.label}</span>}
                    
                    {isCollapsed && (
                      <div className="absolute left-full ml-4 px-3 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-[60]">
                        {item.label}
                      </div>
                    )}
                  </Button>
                </Link>
              </li>
            );
          })}
        </ul>
      </nav>

      {/* Kullanıcı Profil Bölümü */}
      <div className="p-4 border-t border-slate-50 dark:border-slate-800">
        <div className={`flex items-center p-2 rounded-2xl transition-colors ${isCollapsed ? "justify-center" : "gap-3 hover:bg-slate-50"}`}>
          <Avatar className="h-10 w-10 border border-white shadow-sm flex-shrink-0">
            <AvatarImage src={currentUser.avatar} alt={currentUser.name} />
            <AvatarFallback className="bg-teal-100 text-teal-700 font-bold">{currentUser.name.charAt(0)}</AvatarFallback>
          </Avatar>
          
          {!isCollapsed && (
            <div className="flex-1 min-w-0">
              <p className="text-sm font-bold truncate text-slate-900 dark:text-white leading-none mb-1">
                {currentUser.name}
              </p>
              <p className="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Pro Yazar</p>
            </div>
          )}
        </div>

        {!isCollapsed && (
          <Button 
            variant="ghost" 
            onClick={handleLogout}
            className="w-full mt-4 rounded-xl justify-start gap-3 text-red-500 hover:text-red-600 hover:bg-red-50 transition-all font-medium text-xs uppercase tracking-widest"
          >
            <LogOut className="h-4 w-4" />
            <span>Güvenli Çıkış</span>
          </Button>
        )}
      </div>

      {/* Daraltma Butonu (Hata Giderildi: h-4 w-4 yapıldı) */}
     {/* 🚀 Daraltma Butonu: Artık Beyaz Nokta Değil, Şık Bir Menü Tetikleyici */}
<button
  onClick={() => setIsCollapsed(!isCollapsed)}
  // rounded-full yerine rounded-xl yaparak o "nokta" hissini öldürdük
  // bg-white yerine arka plana daha uyumlu veya hafif transparan bir dokunuş ekleyebilirsin
  className="absolute -right-3 top-24 h-8 w-8 rounded-xl border border-slate-200 bg-white dark:bg-slate-900 shadow-xl flex items-center justify-center text-slate-500 hover:text-teal-600 hover:border-teal-100 transition-all z-[100] active:scale-90"
  aria-label="Menü"
>
  {/* 3 Çizgi (Menu) İkonu */}
  <Menu className="h-5 w-5" />
</button>
    </motion.aside>
  );
}