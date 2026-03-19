import * as React from "react";
import { TrendingUp, Users, Eye, Heart, BarChart3, ArrowUpRight } from "lucide-react";

const stats = [
  { label: "Toplam Görüntülenme", value: "12.4K", icon: Eye, color: "text-blue-500", trend: "+12%" },
  { label: "Okuma Süresi (Dk)", value: "482", icon: BarChart3, color: "text-teal-500", trend: "+5%" },
  { label: "Yeni Takipçi", value: "+18", icon: Users, color: "text-purple-500", trend: "+24%" },
  { label: "Toplam Beğeni", value: "842", icon: Heart, color: "text-red-500", trend: "+18%" },
];

const topPosts = [
  { title: "Understanding React Hooks", views: "4.2K", rate: "92%" },
  { title: "Tailwind CSS v4 Guide", views: "3.1K", rate: "88%" },
  { title: "Mastering TypeScript", views: "2.8K", rate: "85%" },
];

export function AnalyticsPage() {
  return (
    <div className="flex-1 bg-white dark:bg-slate-950 p-6 md:p-10 animate-in fade-in duration-700">
      <div className="max-w-6xl mx-auto">
        
        {/* ÜST BAŞLIK */}
        <header className="mb-12">
          <div className="flex items-center gap-2 text-teal-600 font-bold text-[10px] uppercase tracking-[0.3em] mb-4">
            <BarChart3 className="w-4 h-4" /> <span>Analiz Paneli</span>
          </div>
          <h1 className="font-serif text-5xl italic tracking-tighter text-slate-900 dark:text-white mb-2 leading-none">
            Performans Analizi
          </h1>
          <p className="text-slate-500 italic font-light">Yazılarının ve etkileşimlerinin anlık raporu.</p>
        </header>

        {/* 📋 ÖZET KARTLARI */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
          {stats.map((stat) => (
            <div key={stat.label} className="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 transition-all hover:shadow-2xl hover:shadow-teal-500/5 hover:-translate-y-1">
              <div className="flex justify-between items-start mb-4">
                <div className={`p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-sm ${stat.color}`}>
                  <stat.icon className="w-5 h-5" />
                </div>
                <span className="text-[10px] font-bold text-teal-600 bg-teal-50 dark:bg-teal-900/40 dark:text-teal-400 px-2 py-1 rounded-full border border-teal-100 dark:border-teal-800">
                  {stat.trend}
                </span>
              </div>
              <p className="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{stat.value}</p>
              <p className="text-[10px] text-slate-400 uppercase tracking-widest font-bold mt-1">{stat.label}</p>
            </div>
          ))}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          {/* 📊 HAFTALIK TREND GRAFİĞİ (Karanlık Modda Parlatıldı) */}
          <div className="lg:col-span-2 p-8 bg-slate-50 dark:bg-slate-900 rounded-[3rem] border border-slate-100 dark:border-slate-800 overflow-hidden relative group">
             {/* Arkaplan Parıltısı */}
             <div className="absolute -top-24 -right-24 w-64 h-64 bg-teal-500/5 dark:bg-teal-500/10 blur-[100px] rounded-full" />
             
             <div className="flex justify-between items-center mb-10 relative z-10">
                <h3 className="text-xl font-serif italic text-slate-900 dark:text-white">Haftalık Okunma Trendi</h3>
                <TrendingUp className="w-6 h-6 text-teal-500" />
             </div>
             
             <div className="flex items-end justify-between h-56 gap-3 relative z-10">
                {[40, 70, 45, 90, 65, 80, 50].map((h, i) => (
                  <div key={i} className="flex-1 flex flex-col items-center gap-4 group/bar">
                    {/* Tooltip Simülasyonu */}
                    <div className="opacity-0 group-hover/bar:opacity-100 transition-opacity bg-slate-900 dark:bg-teal-400 text-white dark:text-teal-950 text-[10px] font-bold px-2 py-1 rounded-md mb-[-8px] z-20">
                      {h}K
                    </div>
                    {/* 🚀 Parlayan Çubuklar */}
                    <div 
                        className="w-full bg-slate-200 dark:bg-slate-800 group-hover/bar:bg-teal-500 dark:group-hover/bar:bg-teal-400 rounded-t-2xl transition-all duration-500 shadow-sm shadow-teal-500/20" 
                        style={{ height: `${h}%` }}
                    ></div>
                    <span className="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-tighter">
                        {['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'][i]}
                    </span>
                  </div>
                ))}
             </div>
          </div>

          {/* 🔝 EN İYİ YAZILAR (Sağ Panel) */}
          <div className="p-8 bg-slate-900 dark:bg-slate-900 rounded-[3rem] border border-slate-800 dark:border-slate-800 text-white relative overflow-hidden">
             {/* İçerideki Teal Aksanlar */}
             <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-teal-500 to-transparent opacity-30" />
             
             <h3 className="text-xl font-serif italic mb-8 text-teal-400">En Popüler 3</h3>
             <div className="space-y-8">
                {topPosts.map(post => (
                    <div key={post.title} className="space-y-3 group cursor-default">
                        <div className="flex justify-between text-[11px] font-bold uppercase tracking-widest">
                            <span className="truncate w-32 text-slate-300 group-hover:text-white transition-colors">{post.title}</span>
                            <span className="text-teal-400 font-black">{post.views}</span>
                        </div>
                        {/* Progress Bar: Neon Efekti */}
                        <div className="h-1.5 bg-slate-800 rounded-full overflow-hidden border border-slate-700/50">
                            <div 
                              className="h-full bg-teal-400 transition-all duration-1000 shadow-[0_0_15px_rgba(45,212,191,0.6)]" 
                              style={{ width: post.rate }}
                            ></div>
                        </div>
                    </div>
                ))}
             </div>
             
             <button className="w-full mt-10 py-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2 group">
                Tüm Raporu Gör <ArrowUpRight className="w-3 h-3 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
             </button>
          </div>

        </div>
      </div>
    </div>
  );
}