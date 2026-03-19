import { Search, PlusCircle, Moon, Sun, User, Settings, Bell, LogOut } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "./ui/avatar";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import { currentUser } from "../data/mock-data";
import { useNavigate } from "react-router";
import { useTheme } from "next-themes";

export function Header() {
  const navigate = useNavigate();
  const { theme, setTheme } = useTheme();

  return (
    <header className="sticky top-0 z-40 border-b border-slate-100 dark:border-slate-800 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md transition-all duration-300">
      <div className="container mx-auto px-6 py-4">
        <div className="flex items-center justify-between gap-8">
          
          {/* 🔍 ARAMA ÇUBUĞU (Sol Taraf) */}
          <div className="relative flex-1 max-w-xl group">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 group-focus-within:text-teal-600 transition-colors" />
            <Input
              type="search"
              placeholder="Hikaye keşfet..."
              className="pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus-visible:ring-2 ring-teal-500/20 transition-all placeholder:text-slate-400 w-full"
            />
          </div>

          {/* 🚀 SAĞ TARAF GRUBU (En Sağa Yaslı) */}
          <div className="flex items-center gap-3 ml-auto">
            
            {/* 🌙 TEMA DEĞİŞTİRİCİ */}
            <Button
              variant="ghost"
              size="icon"
              className="rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
            >
              <Sun className="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0 text-amber-500" />
              <Moon className="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100 text-teal-500" />
            </Button>

            {/* ✍️ YAZI YAZ BUTONU */}
            <Button
              onClick={() => navigate("/write")}
              className="bg-slate-950 dark:bg-white dark:text-slate-950 text-white hover:bg-teal-600 dark:hover:bg-teal-50 rounded-full gap-2 px-6 shadow-lg shadow-teal-500/5 transition-all active:scale-95"
            >
              <PlusCircle className="h-4 w-4" />
              <span className="hidden lg:inline font-bold tracking-tight">Write a Post</span>
            </Button>

            {/* 👤 PROFİL DROPDOWN */}
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="relative h-10 w-10 rounded-full ring-2 ring-white dark:ring-slate-800 shadow-sm hover:ring-teal-500/20 transition-all">
                  <Avatar className="h-10 w-10">
                    <AvatarImage src={currentUser.avatar} alt={currentUser.name} />
                    <AvatarFallback className="bg-teal-50 text-teal-700 font-bold">{currentUser.name.charAt(0)}</AvatarFallback>
                  </Avatar>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-64 p-2 rounded-[2rem] shadow-2xl border-slate-100 dark:border-slate-800">
                <DropdownMenuLabel className="p-4">
                  <div className="flex flex-col space-y-1">
                    <p className="text-sm font-bold text-slate-900 dark:text-white leading-none">{currentUser.name}</p>
                    <p className="text-[10px] text-slate-400 uppercase tracking-widest font-bold">@{currentUser.username}</p>
                  </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator className="bg-slate-50 dark:bg-slate-800" />
                <div className="p-1">
                  <DropdownMenuItem onClick={() => navigate("/profile")} className="rounded-xl gap-3 p-3 cursor-pointer">
                    <User className="w-4 h-4" /> Profile
                  </DropdownMenuItem>
                  <DropdownMenuItem onClick={() => navigate("/notifications")} className="rounded-xl gap-3 p-3 cursor-pointer">
                    <Bell className="w-4 h-4" /> Notifications
                  </DropdownMenuItem>
                  <DropdownMenuItem onClick={() => navigate("/settings")} className="rounded-xl gap-3 p-3 cursor-pointer">
                    <Settings className="w-4 h-4" /> Settings
                  </DropdownMenuItem>
                </div>
                <DropdownMenuSeparator className="bg-slate-50 dark:bg-slate-800" />
                <DropdownMenuItem className="rounded-xl gap-3 p-3 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 cursor-pointer m-1 font-bold">
                  <LogOut className="w-4 h-4" /> Log out
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </div>
    </header>
  );
}