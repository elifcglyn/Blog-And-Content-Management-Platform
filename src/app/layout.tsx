"use client";

import { useEffect } from "react";
import { Outlet, useNavigate } from "react-router";
import { CollapsibleSidebar } from "./components/collapsible-sidebar";
import { Header } from "./components/header"; // 🚀 Header'ı buraya ekledik!
import { ThemeProvider } from "next-themes";

// ⚠️ BURADAKİ "export" KELİMESİ ROUTER HATASINI ÇÖZER!
export function Layout() {
  const navigate = useNavigate();

  useEffect(() => {
    const isAuth = localStorage.getItem("isAuthenticated");
    if (!isAuth) {
      navigate("/auth");
    }
  }, [navigate]);

  return (
    <ThemeProvider attribute="class" defaultTheme="dark" enableSystem>
      <div className="h-screen flex overflow-hidden bg-white dark:bg-slate-950 transition-colors duration-300">
        
        {/* SOL: Sidebar */}
        <CollapsibleSidebar />

        {/* SAĞ: Ana İçerik Alanı */}
        <div className="flex-1 flex flex-col min-w-0 overflow-hidden relative">
          
          {/* 🚀 ÜST: Header artık tüm alt sayfalarda (İstatistik, Kaydedilenler vb.) sabit! */}
          <Header /> 

          {/* 📄 ALT: Sayfaların görüneceği alan */}
          <main className="flex-1 overflow-y-auto relative p-0 md:p-2">
            <div className="min-h-full bg-white dark:bg-slate-950 rounded-tl-[2rem] md:rounded-tl-[3rem] transition-all">
               <Outlet /> 
            </div>
          </main>
        </div>
      </div>
    </ThemeProvider>
  );
}